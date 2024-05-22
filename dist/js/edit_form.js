(Drupal => {
  Drupal.behaviors.ginEditForm = {
    attach: (context, settings) => {
      once("ginEditForm", ".region-content form.gin-node-edit-form", context).forEach((form => {
        const sticky = context.querySelector(".gin-sticky"), newParent = context.querySelector(".region-sticky__items__inner");
        if (newParent && 0 === newParent.querySelectorAll(".gin-sticky").length) {
          newParent.appendChild(sticky);
          const actionButtons = newParent.querySelectorAll("button, input, select, textarea"), formLabels = newParent.querySelectorAll("label");
          actionButtons.length > 0 && (actionButtons.forEach((el => {
            let element_id = el.getAttribute("id"), new_id = element_id + "--gin-edit-form";
            el.setAttribute("form", form.getAttribute("id")), el.setAttribute("id", new_id),
            settings.ajax && settings.ajax.hasOwnProperty(element_id) && (settings.ajax[new_id] = settings.ajax[element_id],
            settings.ajax[new_id].selector = "#" + new_id, delete settings.ajax[element_id]);
          })), formLabels.forEach((el => {
            el.setAttribute("for", el.getAttribute("for") + "--gin-edit-form");
          })));
        }
      }));
    }
  };
})(Drupal);