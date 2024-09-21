/* To inject this as early as possible
 * we use native JS instead of Drupal's behaviors.
*/

// Legacy Check: Transform old localStorage items to newer ones.
function checkLegacy() {
  if (localStorage.getItem('GinDarkMode')) {
    localStorage.setItem('Drupal.gin.darkmode', localStorage.getItem('GinDarkMode'));
    localStorage.removeItem('GinDarkMode');
  }

  if (localStorage.getItem('GinSidebarOpen')) {
    localStorage.setItem('Drupal.gin.toolbarExpanded', localStorage.getItem('GinSidebarOpen'));
    localStorage.removeItem('GinSidebarOpen');
  }
}

checkLegacy();

// Global var for Gin Darkmode.
window.ginDarkmode = 'auto';

// Initialize ginDarkmode and set classes.
function ginInitDarkmode() {

  if (!localStorage.getItem('Drupal.gin.darkmode') && typeof drupalSettings === 'undefined') {
    // No localStorage is set and drupalSettings are not loaded - come back kater.
    return;
  }

  // Load darkmode setting from localstorare or drupalSettings.
  let darkmode = localStorage.getItem('Drupal.gin.darkmode') || drupalSettings.gin.darkmode;

  if (darkmode == 0 || darkmode == 1) {
    // Fixed preset.
    window.ginDarkmode = darkmode;
  } else {
    // Unset or auto,
    darkmode = window.matchMedia('(prefers-color-scheme: dark)').matches ? 1 : 0;
  }

  // Darkmode class.
  const darkModeClass = 'gin--dark-mode';

  // Set classes for darkmode.
  if (darkmode == 1) {
    document.documentElement.classList.add(darkModeClass);
  } else {
    document.documentElement.classList.contains(darkModeClass) === true && document.documentElement.classList.remove(darkModeClass);
  }

  // Store in localStorage to avoid flickering.
  if (typeof drupalSettings !== 'undefined') {
    if (drupalSettings.gin.darkmode_localstorage == 'always' || drupalSettings.gin.darkmode_localstorage == 'adminpath' && drupalSettings.path.currentPathIsAdmin)  {
      localStorage.setItem('Drupal.gin.darkmode', window.ginDarkmode)
    }
  }
}

ginInitDarkmode();

// GinDarkMode is not set yet or config changes detected.
window.addEventListener('DOMContentLoaded', () => {
  ginInitDarkmode();
});

// Toolbar Check.
if (localStorage.getItem('Drupal.gin.toolbarExpanded')) {
  const style = document.createElement('style');
  const className = 'gin-toolbar-inline-styles';
  style.className = className;

  if (localStorage.getItem('Drupal.gin.toolbarExpanded') === 'true') {
    style.innerHTML = `
    @media (min-width: 976px) {
      /* Small CSS hack to make sure this has the highest priority */
      body.gin--vertical-toolbar.gin--vertical-toolbar.gin--vertical-toolbar {
        padding-inline-start: 256px !important;
        transition: none !important;
      }

      .gin--vertical-toolbar .toolbar-menu-administration {
        min-width: var(--gin-toolbar-width, 256px);
        transition: none;
      }

      .gin--vertical-toolbar .toolbar-menu-administration > .toolbar-menu > .menu-item > .toolbar-icon,
      .gin--vertical-toolbar .toolbar-menu-administration > .toolbar-menu > .menu-item > .toolbar-box > .toolbar-icon {
        min-width: calc(var(--gin-toolbar-width, 256px) - 16px);
      }
    }
    `;

    const scriptTag = document.querySelector('script');
    scriptTag.parentNode.insertBefore(style, scriptTag);
  } else if (document.getElementsByClassName(className).length > 0) {
    document.getElementsByClassName(className)[0].remove();
  }
}

// Sidebar checks.
if (localStorage.getItem('Drupal.gin.sidebarWidth')) {
  const sidebarWidth = localStorage.getItem('Drupal.gin.sidebarWidth');
  document.documentElement.style.setProperty('--gin-sidebar-width', sidebarWidth);
}

if (localStorage.getItem('Drupal.gin.sidebarExpanded.desktop')) {
  const style = document.createElement('style');
  const className = 'gin-sidebar-inline-styles';
  style.className = className;

  if (window.innerWidth < 1024 || localStorage.getItem('Drupal.gin.sidebarExpanded.desktop') === 'false') {
    style.innerHTML = `
    body {
      --gin-sidebar-offset: 0px;
      padding-inline-end: 0;
      transition: none;
    }

    .layout-region-node-secondary {
      transform: translateX(var(--gin-sidebar-width, 360px));
      transition: none;
    }

    .meta-sidebar__overlay {
      display: none;
    }
    `;

    const scriptTag = document.querySelector('script');
    scriptTag.parentNode.insertBefore(style, scriptTag);
  } else if (document.getElementsByClassName(className).length > 0) {
    document.getElementsByClassName(className)[0].remove();
  }
}
