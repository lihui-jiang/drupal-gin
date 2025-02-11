<?php

/**
 * @file
 * Post update file for the Gin theme.
 */

/**
 * Set default user enabled settings for existing installs.
 */
function gin_post_update_set_enabled_user_theme_settings() {
  $config = \Drupal::configFactory()->getEditable('gin.settings');
  $settings = [
    'enable_darkmode',
    'accent_color',
    'focus_color',
    'high_contrast_mode',
    'classic_toolbar',
    'sticky_action_buttons',
    'layout_density',
    'show_description_toggle',
  ];
  if ($config->get('show_user_theme_settings') === TRUE) {

    // Maintain the status quo of all settings enabled when show user theme
    // settings is enabled on a site.
    $config->set('enabled_user_theme_settings', array_combine($settings, $settings))->save();
  }
  else {

    // Set the default to nothing enabled.
    $config->set('enabled_user_theme_settings', array_fill_keys($settings, 0))->save();
  }
}
