(function(s, i, p) {
  const f = function(e) {
    let t = document.querySelector(`link[neo-tooltip-animation-${e}]`);
    t || (t = document.createElement("link"), t.rel = "stylesheet", t.type = "text/css", t.media = "screen", t.href = "/" + i.neoTooltip.dir + `/dist/css/tippy-${e}.css`, t.setAttribute(`neo-tooltip-animation-${e}`, ""), document.getElementsByTagName("head")[0].appendChild(t));
  };
  s.behaviors.neoTooltip = {
    instances: [],
    attach: (e) => {
      typeof i.neoTooltip > "u" || p("neo.tooltip", ".use-neo-tooltip", e).forEach((t) => {
        const o = Object.assign({}, {
          theme: "neo",
          inertia: !0
        }, i.neoTooltip), a = t.getAttribute("data-tippy-animation") || o.animation;
        if (a && ["shift-toward", "shift-away", "scale", "perspective"].includes(a) && f(a), t.getAttribute("data-tippy-trigger-nearest")) {
          const n = t.closest("a, input, button");
          if (n && n !== t)
            o.triggerTarget = [n];
          else {
            const l = t.closest("label");
            if (l)
              o.triggerTarget = [l];
            else {
              const c = t.closest("input");
              c && (o.triggerTarget = [c]);
            }
          }
        }
        const r = t.getAttribute("data-tippy-template");
        r && i.neoTooltipTemplates && i.neoTooltipTemplates[r] && (o.allowHTML = !0, o.interactive = !0, o.content = i.neoTooltipTemplates[r]), o.onShow = (n) => n.props.content.length != 0, s.behaviors.neoTooltip.instances.push(tippy(t, o));
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
})(Drupal, drupalSettings, once);
//# sourceMappingURL=tooltip.js.map
