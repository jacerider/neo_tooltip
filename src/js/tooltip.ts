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
    instances: [] as any,

    attach: (context:HTMLElement) => {
      Drupal.behaviors.neoTooltip.hideAll();

      if (typeof drupalSettings.neoTooltip === 'undefined') {
        return;
      }
      once('neo.tooltip', '.use-neo-tooltip', context).forEach(el => {
        const options:any = Object.assign({}, {
          theme: 'neo',
          inertia: true,
          maxWidth: 600,
        }, drupalSettings.neoTooltip);
        const animation = el.getAttribute('data-tippy-animation') || options.animation;
        if (animation && ['shift-toward', 'shift-away', 'scale', 'perspective'].includes(animation)) {
          loadAnimationStyle(animation);
        }
        const triggerToNearest = el.getAttribute('data-tippy-trigger-nearest');
        if (triggerToNearest) {
          const closest = el.closest('a, input, button');
          if (closest && closest !== el) {
            options.triggerTarget = [closest];
          }
          else {
            // Bind to some parent form elements.
            const closestLabel = el.closest('label');
            if (closestLabel) {
              options.triggerTarget = [closestLabel];
            }
            else {
              const closestInput = el.closest('input');
              if (closestInput) {
                options.triggerTarget = [closestInput];
              }
            }
          }
        }
        const template = el.getAttribute('data-tippy-template');
        if (template && drupalSettings.neoTooltipTemplates && drupalSettings.neoTooltipTemplates[template]) {
          options['allowHTML'] = true;
          options['interactive'] = true;
          options['content'] = drupalSettings.neoTooltipTemplates[template];
        }
        options.onShow = (instance:any) => {
          if (instance.props.content.length == 0) {
            return false;
          }
          return true;
        };
        Drupal.behaviors.neoTooltip.instances.push(tippy(el, options));
      });
    },

    hideAll: () => {
      Drupal.behaviors.neoTooltip.instances.forEach((instance:any) => {
        instance.hide();
      });
    },

    disableAll: () => {
      Drupal.behaviors.neoTooltip.instances.forEach((instance:any) => {
        instance.disable();
      });
    },

    enableAll: () => {
      Drupal.behaviors.neoTooltip.instances.forEach((instance:any) => {
        instance.enable();
      });
    }
  };

})(Drupal, drupalSettings, once);

export {};
