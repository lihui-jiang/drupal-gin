/* eslint-disable func-names, no-mutable-exports, comma-dangle, strict */

'use strict';

((Drupal) => {
  Drupal.behaviors.ginEditForm = {
    attach: (context, settings) => {
      once('ginEditForm', '.region-content form.gin-node-edit-form', context).forEach(form => {
        const sticky = context.querySelector('.gin-sticky');
        const newParent = context.querySelector('.region-sticky__items__inner');

        if (newParent && newParent.querySelectorAll('.gin-sticky').length === 0) {
          newParent.appendChild(sticky);

          // Attach form elements to main form
          const actionButtons = newParent.querySelectorAll('button, input, select, textarea');
          const formLabels = newParent.querySelectorAll('label');

          if (actionButtons.length > 0) {
            actionButtons.forEach((el) => {
              let element_id = el.getAttribute('id');
              let new_id = element_id + '--gin-edit-form';
              el.setAttribute('form', form.getAttribute('id'));
              el.setAttribute('id', new_id);

              if (settings.ajax && settings.ajax.hasOwnProperty(element_id)) {
                settings.ajax[new_id] = settings.ajax[element_id];
                settings.ajax[new_id].selector = '#' + new_id;
                delete settings.ajax[element_id];
              }
            });

            formLabels.forEach((el => {
              el.setAttribute('for', el.getAttribute('for') + '--gin-edit-form');
            }));
          }
        }
      });
    }
  };
})(Drupal);
