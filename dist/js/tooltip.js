(function(s, o, l, p) {
  const h = function(e) {
    let t = document.querySelector(`link[neo-tooltip-animation-${e}]`);
    t || (t = document.createElement("link"), t.rel = "stylesheet", t.type = "text/css", t.media = "screen", t.href = "/" + o.neoTooltip.dir + `/dist/css/tippy-${e}.css`, t.setAttribute(`neo-tooltip-animation-${e}`, ""), document.getElementsByTagName("head")[0].appendChild(t));
  };
  s.behaviors.neoTooltip = {
    instances: [],
    attach: function(e) {
      this.hideAll(), !(typeof o.neoTooltip > "u") && l("neo-tooltip", ".use-neo-tooltip", e).forEach((t) => {
        t.classList.contains("form-checkboxes") ? t.querySelectorAll(".form-type--checkbox").forEach((i) => {
          this.addInstance(i);
        }) : this.addInstance(t);
      });
    },
    addInstance: function(e, t) {
      t = Object.assign({}, this.getOptions(e), t), this.instances.push(p(e, t));
    },
    getOptions: (e) => {
      const t = Object.assign({}, {
        theme: "neo",
        inertia: !0,
        maxWidth: 600
      }, o.neoTooltip), i = e.getAttribute("data-tippy-animation") || t.animation;
      if (i && ["shift-toward", "shift-away", "scale", "perspective"].includes(i) && h(i), e.getAttribute("data-tippy-trigger-nearest")) {
        const n = e.closest("a, input, button");
        if (n && n !== e)
          t.triggerTarget = [n];
        else {
          const c = e.closest("label");
          if (c)
            t.triggerTarget = [c];
          else {
            const r = e.closest("input");
            r && (t.triggerTarget = [r]);
          }
        }
      }
      const a = e.getAttribute("data-tippy-template");
      return a && o.neoTooltipTemplates && o.neoTooltipTemplates[a] && (t.allowHTML = !0, t.interactive = !0, t.content = o.neoTooltipTemplates[a]), t.onShow = (n) => n.props.content.length != 0, t;
    },
    hideAll: () => {
      s.behaviors.neoTooltip.instances.forEach((e) => {
        e.hide();
      });
    },
    disableAll: () => {
      s.behaviors.neoTooltip.instances.forEach((e) => {
        e.disable();
      });
    },
    enableAll: () => {
      s.behaviors.neoTooltip.instances.forEach((e) => {
        e.enable();
      });
    }
  };
})(Drupal, drupalSettings, once, tippy);
//# sourceMappingURL=tooltip.js.map
