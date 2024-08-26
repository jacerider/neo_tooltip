(function (Drupal, drupalSettings, once) {

  const loadAnimationStyle = function (animation:string) {
    let stylesheet:HTMLLinkElement|null = document.querySelector(`link[neo-tooltip-animation-${animation}]`);
    if (!stylesheet) {
      stylesheet = document.createElement('link');
      stylesheet.rel = 'stylesheet';
      stylesheet.type = 'text/css';
      stylesheet.media = 'screen';
      stylesheet.href = '/' + drupalSettings.neoTooltip.dir + `/dist/css/tippy-${animation}.css`;
      stylesheet.setAttribute(`neo-tooltip-animation-${animation}`, '');
      document.getElementsByTagName('head')[0].appendChild(stylesheet);
    }
  }

  Drupal.behaviors.neoTooltip = {
    attach: (context:HTMLElement) => {
      if (typeof drupalSettings.neoTooltip === 'undefined') {
        return;
      }
      once('neo.tooltip', '.use-neo-tooltip', context).forEach(el => {
        const options:any = Object.assign({}, {
          theme: 'neo',
          inertia: true,
        }, drupalSettings.neoTooltip);
        const animation = el.getAttribute('data-tippy-animation') || options.animation;
        if (animation && ['shift-toward', 'shift-away', 'scale', 'perspective'].includes(animation)) {
          loadAnimationStyle(animation);
        }
        const triggerToNearest = el.getAttribute('data-tippy-trigger-nearest');
        if (triggerToNearest) {
          const closest = el.closest('a, input, button');
          if (closest) {
            options.triggerTarget = closest;
          }
          else {
            const closestFormElements = [];
            const closestFormElementWrapper = el.closest('.form-item');
            if (closestFormElementWrapper) {
              closestFormElements.push(closestFormElementWrapper.querySelector('input'));
              closestFormElements.push(closestFormElementWrapper.querySelector('label'));
            }
            if (closestFormElements.length) {
              options.triggerTarget = closestFormElements;
            }
          }
        }
        const isTemplate = el.getAttribute('data-tippy-template');
        if (isTemplate) {
          options['allowHTML'] = true;
          options['content'] = (ref:HTMLElement) => {
            let template = ref.nextElementSibling;
            if (template && template.tagName === 'TEMPLATE') {
              return template.innerHTML;
            }
            template = ref.querySelector('.neo-tooltip-template');
            if (template && template.tagName === 'TEMPLATE') {
              return template.innerHTML;
            }
            return '';
          };
        }
        tippy(el, options);
      });
    }
  };

})(Drupal, drupalSettings, once);

export {};
