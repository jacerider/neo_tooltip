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
        // One element both opens the tooltip and is pointed at by it.
        //
        // These two used to be able to disagree, and the result was a tooltip
        // that appeared nowhere near what you reached for. `triggerTarget` only
        // says what opens it; tippy keeps measuring whatever it was constructed
        // on. On a checkbox or radio that is the form item — the full width of
        // the row — so hovering a chip at the left opened a tooltip centred
        // over the middle of the row, 300px away.
        //
        // Order matters. A wrapper is asked what it stands in for before the
        // ancestor walk runs, because the thing on screen is inside it, not
        // above it: a select is replaced by `.ts-wrapper`, and a checkbox is
        // drawn by its label while the input behind it is `sr-only` and 1px.
        // Querying ancestors finds neither.
        const inside = (sel:string) => el.querySelector<HTMLElement>(sel);
        const closest = el.closest<HTMLElement>('a, input, button');
        // The help icon is looked up from the form item, not from `el`. On a
        // text field `el` is the input itself and the icon is its sibling in
        // the label, so searching `el` would never find it.
        const item = el.closest<HTMLElement>('.js-form-item, .form-item');
        // A badge inside a `visually-hidden` label is clipped to a 0x0 box.
        // Anchoring to that is worse than not anchoring at all: there is
        // nothing to hover, and the tooltip is measured against an empty rect
        // in the page's top-left corner. ElementProcess hands those cases to
        // the group legend instead, and this is the guard for any that reach
        // here another way.
        const onScreen = (n:HTMLElement|null) =>
          n && !n.closest('.visually-hidden, .sr-only') ? n : null;
        // A control with no visible label of its own has no badge beside it —
        // its badge is drawn on the enclosing legend or summary, which is
        // outside the form item entirely. The two ends carry the same id,
        // because a group may hold more than one described control and
        // position alone cannot say which badge belongs to which.
        const helpId = el.getAttribute('data-neo-tooltip-help-id');
        const promoted = helpId
          ? document.querySelector<HTMLElement>(`[data-neo-tooltip-help="${CSS.escape(helpId)}"]`)
          : null;
        // A single checkbox or radio, not a group of them. The group's tooltip
        // describes the whole set, so it stays anchored to the set.
        const isBoolean = el.matches('.form-type--checkbox, .form-type--radio, .form-type--boolean, .js-form-type-checkbox, .js-form-type-radio');
        const isGroup = el.matches('.form-composite, .fieldgroup, .form-type--checkboxes, .form-type--radios');
        const anchor =
          // A help icon on the group that names this control.
          onScreen(promoted)
          // A help icon beside the label, where one is rendered.
          || onScreen(item && item.querySelector<HTMLElement>('[data-neo-tooltip-help]'))
          // The control a wrapper stands in for, most specific first.
          || inside('.ts-wrapper')
          // A checkbox or radio is drawn by a label. Which label depends on the
          // template that ran: the grouped path marks it `.option`, while a
          // single checkbox in a button style is wrapped by a plain one. Both
          // are the visible control, so take whichever is there.
          || (isBoolean ? (inside('label.option') || inside('label')) : null)
          // A group's tooltip describes the whole set, so it stays on the set
          // rather than picking whichever option happens to come first.
          || (isGroup ? null : inside('select, textarea, input:not([type="hidden"]):not(.sr-only)'))
          // Failing that, the nearest focusable ancestor — the original rule.
          || (closest && closest !== el ? closest : null)
          || el.closest<HTMLElement>('label')
          || el.closest<HTMLElement>('input');

        if (anchor && anchor !== el) {
          options.triggerTarget = [anchor];
          // A function, not a captured rect: tippy calls it on every
          // reposition, so the tooltip follows the anchor through scroll,
          // resize and anything else that reflows the row.
          options.getReferenceClientRect = () => anchor.getBoundingClientRect();
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
