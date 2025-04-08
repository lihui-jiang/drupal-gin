<?php

namespace Drupal\Tests\gin\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Tests the gin admin theme.
 *
 * @group gin
 */
class GinTest extends BrowserTestBase {

  /**
   * Modules to enable.
   *
   * Install the shortcut module so that gin.settings has its schema checked.
   * There's currently no way for Gin to provide a default and have valid
   * configuration as themes cannot react to a module install.
   *
   * @var string[]
   */
  protected static $modules = [
    'shortcut',
    'toolbar',
    'node',
  ];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * Sets up the test.
   */
  protected function setUp(): void {
    parent::setUp();

    $this->assertTrue(\Drupal::service('theme_installer')->install(['gin']));
    $this->container->get('config.factory')
      ->getEditable('system.theme')
      ->set('default', 'gin')
      ->set('admin', 'gin')
      ->save();

    $adminUser = $this->drupalCreateUser([
      'access administration pages',
      'administer themes',
      'access toolbar',
      'access content overview',
    ]);
    $this->drupalLogin($adminUser);
  }

  /**
   * Tests that the Gin theme always adds its message CSS and Classy's.
   */
  public function testDefaultGinSettings() {
    $response = $this->drupalGet('/admin/content');
    $this->assertSession()->statusCodeEquals(200);
    $this->assertStringContainsString('"darkmode":"0"', $response);
    $this->assertStringContainsString('"preset_accent_color":"blue"', $response);
    $this->assertStringContainsString('"preset_focus_color":"gin"', $response);
    $this->assertSession()->responseContains('gin.css');
    $this->assertSession()->responseContains('toolbar.css');
    $this->assertSession()->responseNotContains('classic_toolbar.css');
  }

  /**
   * Tests Darkmode setting.
   */
  public function testDarkModeSetting() {
    \Drupal::configFactory()->getEditable('gin.settings')->set('enable_darkmode', '1')->save();
    $response = $this->drupalGet('/admin/content');
    $this->assertSession()->statusCodeEquals(200);
    $this->assertStringContainsString('"darkmode":"1"', $response);
  }

  /**
   * Tests Classic Drupal Toolbar setting.
   */
  public function testClassicToolbarSetting() {
    \Drupal::configFactory()->getEditable('gin.settings')->set('classic_toolbar', 'classic')->save();
    $this->drupalGet('/admin/content');
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->responseContains('classic_toolbar.css');
  }

  /**
   * Tests Color Accent setting.
   */
  public function testAccentColorSetting() {
    \Drupal::configFactory()->getEditable('gin.settings')->set('preset_accent_color', 'red')->save();
    $response = $this->drupalGet('/admin/content');
    $this->assertSession()->statusCodeEquals(200);
    $this->assertStringContainsString('"preset_accent_color":"red"', $response);
  }

  /**
   * Tests Focus Accent setting.
   */
  public function testFocusColorSetting() {
    \Drupal::configFactory()->getEditable('gin.settings')->set('preset_focus_color', 'blue')->save();
    $response = $this->drupalGet('/admin/content');
    $this->assertSession()->statusCodeEquals(200);
    $this->assertStringContainsString('"preset_focus_color":"blue"', $response);
  }

  /**
   * Test user settings.
   */
  public function testUserSettings() {
    \Drupal::configFactory()->getEditable('gin.settings')
      ->set('show_user_theme_settings', TRUE)
      ->set('enabled_user_theme_settings', [
        'sticky_action_buttons',
        'enable_darkmode',
      ])
      ->save();

    $user1 = $this->createUser();
    $this->drupalLogin($user1);

    $this->assertStringContainsString('"darkmode":"0"', $this->drupalGet($user1->toUrl('edit-form')));

    // Check that non-enabled settings do not appear.
    $this->assertSession()->pageTextNotContains('Increase contrast');

    // Enable the high contrast mode expected later in the test.
    \Drupal::configFactory()->getEditable('gin.settings')
      ->set('enabled_user_theme_settings', [
        'sticky_action_buttons',
        'enable_darkmode',
        'high_contrast_mode',
      ])
      ->save();

    // Change something on the logged in user form.
    $this->submitForm([
      'sticky_action_buttons' => TRUE,
      'enable_user_settings' => TRUE,
      'enable_darkmode' => '1',
    ], 'Save');
    $this->assertStringContainsString('"darkmode":"1"', $this->drupalGet($user1->toUrl('edit-form')));

    // Check that high contrast mode now appears as an option.
    $this->drupalGet($user1->toUrl('edit-form'));
    $this->assertSession()->pageTextContains('Increase contrast');

    // Login as admin.
    $this->drupalLogin($this->rootUser);
    $this->assertStringContainsString('"darkmode":"0"', $this->drupalGet('edit-form'));

    // Change something on user1 edit form.
    $this->drupalGet($user1->toUrl('edit-form'));
    $this->submitForm([
      'enable_user_settings' => TRUE,
      'high_contrast_mode' => TRUE,
      'enable_darkmode' => '1',
    ], 'Save');

    // Check logged-in's user is not affected.
    $loggedInUserResponse = $this->drupalGet('edit-form');
    $this->assertStringContainsString('"highcontrastmode":false', $loggedInUserResponse);
    $this->assertStringContainsString('"darkmode":"0"', $loggedInUserResponse);

    // Check settings of user1.
    $this->drupalLogin($user1);
    $rootUserResponse = $this->drupalGet($user1->toUrl('edit-form'));
    $this->assertStringContainsString('"highcontrastmode":true', $rootUserResponse);
    $this->assertStringContainsString('"darkmode":"1"', $rootUserResponse);

    // Install the overrides test to check that the API now prevents access.
    $success = $this->container->get('module_installer')->install(['gin_overrides_test'], TRUE);
    $this->assertTrue($success);
    $rootUserResponse = $this->drupalGet($user1->toUrl('edit-form'));
    $this->assertStringContainsString('"highcontrastmode":false', $rootUserResponse);
    $this->assertStringContainsString('"darkmode":"0"', $rootUserResponse);
    // Disable the module again and confirm swap back in order to ensure
    // subsequent tests works fine.
    $success = $this->container->get('module_installer')->uninstall(['gin_overrides_test'], FALSE);
    $this->assertTrue($success);
    $rootUserResponse = $this->drupalGet($user1->toUrl('edit-form'));
    $this->assertStringContainsString('"highcontrastmode":true', $rootUserResponse);
    $this->assertStringContainsString('"darkmode":"1"', $rootUserResponse);

    // Prevent the high contrast mode from being overridden by removing it from
    // enabled settings. Expect to see high contrast mode disabled again for
    // user 1.
    \Drupal::configFactory()->getEditable('gin.settings')
      ->set('enabled_user_theme_settings', [
        'sticky_action_buttons',
        'enable_darkmode',
      ])
      ->save();
    $rootUserResponse = $this->drupalGet($user1->toUrl('edit-form'));
    $this->assertStringContainsString('"highcontrastmode":false', $rootUserResponse);
    $this->assertStringContainsString('"darkmode":"1"', $rootUserResponse);

    // Enable all settings to ensure user storage if each option takes place.
    \Drupal::configFactory()->getEditable('gin.settings')
      ->set('enabled_user_theme_settings', [
        'enable_darkmode',
        'accent_color',
        'focus_color',
        'high_contrast_mode',
        'classic_toolbar',
        'sticky_action_buttons',
        'layout_density',
        'show_description_toggle',
      ])
      ->save();
    $this->drupalGet($user1->toUrl('edit-form'));
    $settings = [
      'enable_darkmode' => '1',
      'preset_accent_color' => 'pink',
      'accent_color' => '#333333',
      'preset_focus_color' => 'orange',
      'focus_color' => '#444444',
      'high_contrast_mode' => TRUE,
      'classic_toolbar' => 'classic',
      'sticky_action_buttons' => 1,
      'layout_density' => 'small',
      'show_description_toggle' => 0,
    ];
    $this->submitForm($settings, 'Save', 'user-form');
    $user_data = \Drupal::service('user.data')->get('gin', $user1->id(), 'settings');
    ksort($user_data);
    ksort($settings);
    $this->assertSame($user_data, $settings);

    // Now remove most settings to ensure that resaving without any change
    // clears those values.
    \Drupal::configFactory()->getEditable('gin.settings')
      ->set('enabled_user_theme_settings', [
        'enable_darkmode',
      ])
      ->save();
    $this->drupalGet($user1->toUrl('edit-form'));
    $this->submitForm([], 'Save', 'user-form');
    $user_data = \Drupal::service('user.data')->get('gin', $user1->id(), 'settings');
    $this->assertSame(['enable_darkmode' => '1'], $user_data);
  }

}
