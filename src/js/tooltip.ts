(function (Drupal, drupalSettings, once, tippy) {

  const loadAnimationStyle = function (animation:string) {
    let stylesheet:HTMLLinkElement|null = document.querySelector(`link[neo-tooltip-animation-${animation}]`);
    if (!stylesheet) {
      stylesheet = document.createElement('link');
      stylesheet.rel = 'stylesheet';
      stylesheet.type = 'text/css';
      stylesheet.media = 'screen';
      stylesheet.href = '/' + drupalSettings.neoTooltip.dir + `/lib/css/tippy-${animation}.css`;
      stylesheet.setAttribute(`neo-tooltip-animation-${animation}`, '');
      document.getElementsByTagName('head')[0].appendChild(stylesheet);
    }
  }

  Drupal.behaviors.neoTooltip = {
    instances: [] as any,

    attach: function (context:HTMLElement) {
      if (context && context.id && context.id.startsWith('tippy-')) {
        // Do not hide if the context is a tooltip itself.
      }
      else {
        this.hideAll();
      }
      if (typeof drupalSettings.neoTooltip === 'undefined') {
        return;
      }
      if (context.classList && context.classList.contains('use-neo-tooltip')) {
        context = context.parentElement || context;
      }
      once('neo-tooltip', '.use-neo-tooltip', context).forEach(el => {
        if (el.classList.contains('form-checkboxes')) {
          // Special handling for checkboxes.
          // We need to add tooltips to each checkbox input.
          el.querySelectorAll('.form-type--checkbox').forEach((checkbox) => {
            this.addInstance(checkbox);
          });
        }
        else {
          this.addInstance(el);
        }
      });
    },

    addInstance: function (el:HTMLElement, options:any) {
      options = Object.assign({}, this.getOptions(el), options);
      delete options.dir;
      this.instances.push(tippy(el, options));
    },

    getOptions: (el: HTMLElement) => {
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
      return options;
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

})(Drupal, drupalSettings, once, tippy);
