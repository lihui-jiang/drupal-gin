/* eslint-disable func-names, no-mutable-exports, comma-dangle, strict */

'use strict';

((Drupal) => {
  Drupal.behaviors.ginEditForm = {
    attach: (context) => {
      const ginEditForm = once('ginEditForm', document.querySelector('.region-content .block-system-main-block form'));
      ginEditForm.forEach(form => {
        const sticky = context.querySelector('.gin-sticky');
        const newParent = document.querySelector('.region-sticky__items__inner');

        if (newParent && !sticky.getAttribute('gin-sticky-applied')) {
          let stickyPresent = newParent.querySelector('.gin-sticky');
          if(stickyPresent) {
            stickyPresent.replaceWith(sticky);
          } else {
            newParent.appendChild(sticky);
          }

          // Attach form elements to main form
          const actionButtons = newParent.querySelectorAll('button, input, select, textarea');

          if (actionButtons.length > 0) {
            actionButtons.forEach((el) => {
              el.setAttribute('form', form.getAttribute('id'));
              el.setAttribute('id', el.getAttribute('id') + '--gin-edit-form');
            });
          }
          sticky.setAttribute('gin-sticky-applied', 1);
        }
      });
    }
  };
})(Drupal);
