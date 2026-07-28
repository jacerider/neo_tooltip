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
      if (context && context.id && typeof context.id === 'string' && context.id.startsWith('tippy-')) {
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
        // If tag name is table, td, tr, or th, do not apply tooltip.
        if (['table', 'td', 'tr', 'th'].includes(el.tagName.toLowerCase())) {
          return;
        }
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

    /**
     * Tear down the instances inside a context before it goes away.
     *
     * Without this, `instances` is append-only. Anything that builds DOM at
     * runtime — this module's own dropbutton popper, or a consumer that clones
     * a prototype per item — leaves a live tippy behind for every element it
     * later discards. Those are never collected, and `hideAll()` walks the
     * whole list on every subsequent attach, so opening any tooltip gets
     * steadily more expensive the longer the page lives.
     *
     * `once.remove()` matters as much as the destroy: a context that is
     * detached and then re-attached — exactly what the dropbutton popper does
     * on every open and close — has to be able to build its tooltips again,
     * and the `once` stamp would otherwise block it forever.
     *
     * Callers that discard DOM must call Drupal.detachBehaviors() on it before
     * removing; an element torn out without that still leaks, because there is
     * no safe way to distinguish it from one that is only temporarily out of
     * the document and on its way back.
     */
    detach: function (context:HTMLElement, _settings:any, trigger:string) {
      // 'serialize' is a form read, not a teardown. 'move' keeps the element —
      // and with it the once stamp and a still-valid instance — alive on the
      // other side, so there is nothing to collect.
      if (trigger !== 'unload') {
        return;
      }
      this.instances = this.instances.filter((instance:any) => {
        const el = instance.reference as HTMLElement;
        // contains() counts a node as containing itself, so this covers both
        // the context being torn down and everything under it.
        if (el && !context.contains(el)) {
          return true;
        }
        instance.destroy();
        return false;
      });
      once.remove('neo-tooltip', '.use-neo-tooltip', context);
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
        // A consumer that destroys an instance directly, rather than through
        // detach(), leaves it in the list; hiding a destroyed instance warns.
        if (instance.state?.isDestroyed) {
          return;
        }
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
