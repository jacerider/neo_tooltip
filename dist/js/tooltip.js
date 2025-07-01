(function(n, o, r, p) {
  const h = function(t) {
    let e = document.querySelector(`link[neo-tooltip-animation-${t}]`);
    e || (e = document.createElement("link"), e.rel = "stylesheet", e.type = "text/css", e.media = "screen", e.href = "/" + o.neoTooltip.dir + `/dist/css/tippy-${t}.css`, e.setAttribute(`neo-tooltip-animation-${t}`, ""), document.getElementsByTagName("head")[0].appendChild(e));
  };
  n.behaviors.neoTooltip = {
    instances: [],
    attach: function(t) {
      this.hideAll(), !(typeof o.neoTooltip > "u") && (t.classList && t.classList.contains("use-neo-tooltip") && (t = t.parentElement || t), r("neo-tooltip", ".use-neo-tooltip", t).forEach((e) => {
        e.classList.contains("form-checkboxes") ? e.querySelectorAll(".form-type--checkbox").forEach((i) => {
          this.addInstance(i);
        }) : this.addInstance(e);
      }));
    },
    addInstance: function(t, e) {
      e = Object.assign({}, this.getOptions(t), e), this.instances.push(p(t, e));
    },
    getOptions: (t) => {
      const e = Object.assign({}, {
        theme: "neo",
        inertia: !0,
        maxWidth: 600
      }, o.neoTooltip), i = t.getAttribute("data-tippy-animation") || e.animation;
      if (i && ["shift-toward", "shift-away", "scale", "perspective"].includes(i) && h(i), t.getAttribute("data-tippy-trigger-nearest")) {
        const s = t.closest("a, input, button");
        if (s && s !== t)
          e.triggerTarget = [s];
        else {
          const c = t.closest("label");
          if (c)
            e.triggerTarget = [c];
          else {
            const l = t.closest("input");
            l && (e.triggerTarget = [l]);
          }
        }
      }
      const a = t.getAttribute("data-tippy-template");
      return a && o.neoTooltipTemplates && o.neoTooltipTemplates[a] && (e.allowHTML = !0, e.interactive = !0, e.content = o.neoTooltipTemplates[a]), e.onShow = (s) => s.props.content.length != 0, e;
    },
    hideAll: () => {
      n.behaviors.neoTooltip.instances.forEach((t) => {
        t.hide();
      });
    },
    disableAll: () => {
      n.behaviors.neoTooltip.instances.forEach((t) => {
        t.disable();
      });
    },
    enableAll: () => {
      n.behaviors.neoTooltip.instances.forEach((t) => {
        t.enable();
      });
    }
  };
})(Drupal, drupalSettings, once, tippy);
//# sourceMappingURL=tooltip.js.map
