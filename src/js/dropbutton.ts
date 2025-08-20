(function (Drupal, drupalSettings, once) {

  Drupal.behaviors.neoTooltipDropbutton = {

    attach: (context:HTMLElement) => {
      if (typeof drupalSettings.neoTooltip === 'undefined') {
        return;
      }

      once('neo.tooltip.dropbutton', '.dropbutton', context).forEach(el => {

        const firstLi = el.querySelector('li');
        if (!firstLi) {
          console.error('No li elements found in the list');
          return;
        }
        el.classList.add('neo-dropbutton');
        firstLi.classList.add('dropbutton-action');

        // Create a new UL element
        const newUl = document.createElement('ul');

        // Get all LI elements from the original UL
        const liElements = el.querySelectorAll('li');

        if (liElements.length <= 1) {
          el.classList.add('dropbutton-single');
          return;
        }
        el.classList.add('dropbutton-multiple');

        // Skip the first LI and clone the rest to add to the new UL
        for (let i = 1; i < liElements.length; i++) {
          const clonedLi = liElements[i].cloneNode(true) as HTMLLIElement;
          clonedLi.querySelector('a')?.removeAttribute('data-once');
          newUl.appendChild(clonedLi);
          liElements[i].classList.add('sr-only');
          liElements[i].querySelector('a')?.removeAttribute('id');
        }

        const triggerLi = document.createElement('li');
        triggerLi.classList.add('dropbutton-toggle');
        const triggerButton = document.createElement('button');
        triggerButton.setAttribute('type', 'button');
        triggerButton.innerHTML = '<span class="dropbutton-arrow"><span class="visually-hidden">' + Drupal.t('List additional actions') + '</span></span>';
        triggerLi.appendChild(triggerButton);

        el.insertBefore(triggerLi, firstLi.nextSibling);

        const options = {} as any;
        options.content = newUl;
        options.theme = 'neo dropbutton';
        options.trigger = 'click';
        options.interactive = true;
        // if (hasNonVisibleOverflowParent(el)) {
        //   options.appendTo = document.body;
        // }
        options.placement = 'bottom-end';
        options.onShow = (instance:any) => {
          Drupal.attachBehaviors(instance.popper, drupalSettings);
          el.classList.add('is-active');
        };
        options.onHide = (_instance:any) => {
          el.classList.remove('is-active');
        };
        Drupal.behaviors.neoTooltip.addInstance(triggerLi, options);
      });
    }
  };

})(Drupal, drupalSettings, once);
