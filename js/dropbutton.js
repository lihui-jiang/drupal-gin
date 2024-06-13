/**
 * @file
 * Theme overrides for Gin.
 */

((Drupal) => {
  Drupal.behaviors.ginDropbutton = {
    attach: function attach(context) {
      const layout_density = document
        .querySelector('body')
        .getAttribute('data-gin-layout-density');
      context
        .querySelectorAll(
          'table tr:not(:first-of-type):nth-last-child(-n+2) td .dropbutton__items',
        )
        .forEach((e) => {
          const { rowIndex } = e.closest('tr');
          let limit;
          switch (layout_density) {
            case 'medium':
              limit = rowIndex * 2 - Math.floor(rowIndex / 3);
              break;
            case 'small':
              limit = rowIndex + Math.floor(rowIndex / 2) + 1;
              break;
            default:
              limit = rowIndex * 2;
              break;
          }

          if (e.children.length >= limit) {
            e.style.bottom = 'unset';
          }
        });
    },
  };
})(Drupal);
