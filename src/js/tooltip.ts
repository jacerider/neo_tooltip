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
        // Where a help icon exists it becomes the *only* trigger, replacing the
        // field rather than joining it.
        //
        // A tooltip bound to the control fires while you are trying to use it:
        // on hover as the pointer crosses the field, and on focus for every
        // described field you tab through — covering the neighbouring row each
        // time. On touch there is no hover at all, so the only thing that opens
        // it is focus, which is the moment the on-screen keyboard appears and
        // the viewport is at its smallest.
        //
        // It is also the half that is hard to make dismissable. WCAG 1.4.13
        // wants hover/focus content dismissable without moving focus, and a
        // tooltip anchored to the field it is describing cannot be closed
        // without leaving the field. Anchored to a button, it opens and closes
        // deliberately — see hideOnEsc below.
        //
        // Nothing is lost for assistive technology: the description is in the
        // accessibility tree either way, via core's markup and the
        // `aria-describedby` the control already carries.
        //
        // Scoped to the form item so one field never picks up the next field's
        // icon, and skipped entirely when the icon is switched off, or the help
        // would have no trigger at all.
        const item = el.closest('.js-form-item, .form-item');
        const help = item && item.querySelector<HTMLElement>('[data-neo-tooltip-help]');
        if (help) {
          options.triggerTarget = [help];
        }
      }
      const template = el.getAttribute('data-tippy-template');
      if (template && drupalSettings.neoTooltipTemplates && drupalSettings.neoTooltipTemplates[template]) {
        options['allowHTML'] = true;
        options['interactive'] = true;
        options['content'] = drupalSettings.neoTooltipTemplates[template];
      }
      // Hand ARIA back to the markup when the trigger is already described.
      // Tippy's default is `aria: {content: 'auto', expanded: 'auto'}`, and on
      // a form field neither half is wanted: 'content' appends a second
      // aria-describedby so the text is announced twice, and 'expanded' — which
      // is what an interactive instance uses instead, meaning every tooltip
      // built from HTML — puts aria-expanded on the field, claiming combobox
      // semantics a textbox does not have. See Tooltip::setDescribedElsewhere().
      if (el.getAttribute('data-tippy-described-elsewhere')) {
        options['aria'] = { content: null, expanded: null };
      }
      // Dismissable without moving focus, which WCAG 1.4.13 asks of anything
      // shown on hover or focus. Tippy's own `hideOnClick` covers the pointer;
      // this is the keyboard half, and it is bound per instance rather than
      // globally so a tooltip inside a dialog cannot swallow the Escape that
      // was meant to close the dialog — it only listens while it is open.
      options.onShow = (instance:any) => {
        if (instance.props.content.length == 0) {
          return false;
        }
        instance._neoEsc = (e:KeyboardEvent) => {
          if (e.key === 'Escape') {
            e.stopPropagation();
            instance.hide();
          }
        };
        document.addEventListener('keydown', instance._neoEsc, true);
        return true;
      };
      options.onHide = (instance:any) => {
        if (instance._neoEsc) {
          document.removeEventListener('keydown', instance._neoEsc, true);
          instance._neoEsc = null;
        }
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
