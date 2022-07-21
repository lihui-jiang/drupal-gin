(Drupal => {
  Drupal.behaviors.ginEditForm = {
    attach: context => {
      once("ginEditForm", document.querySelector(".region-content .block-system-main-block form")).forEach((form => {
        const sticky = context.querySelector(".gin-sticky"), newParent = document.querySelector(".region-sticky__items__inner");
        if (newParent && !sticky.getAttribute("gin-sticky-applied")) {
          let stickyPresent = newParent.querySelector(".gin-sticky");
          stickyPresent ? stickyPresent.replaceWith(sticky) : newParent.appendChild(sticky);
          const actionButtons = newParent.querySelectorAll("button, input, select, textarea");
          actionButtons.length > 0 && actionButtons.forEach((el => {
            el.setAttribute("form", form.getAttribute("id")), el.setAttribute("id", el.getAttribute("id") + "--gin-edit-form");
          })), sticky.setAttribute("gin-sticky-applied", 1);
        }
      }));
    }
  };
})(Drupal);