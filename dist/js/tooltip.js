(function(s, a, c) {
  const p = function(o) {
    let t = document.querySelector(`link[neo-tooltip-animation-${o}]`);
    t || (t = document.createElement("link"), t.rel = "stylesheet", t.type = "text/css", t.media = "screen", t.href = "/" + a.neoTooltip.dir + `/dist/css/tippy-${o}.css`, t.setAttribute(`neo-tooltip-animation-${o}`, ""), document.getElementsByTagName("head")[0].appendChild(t));
  };
  s.behaviors.neoTooltip = {
    instances: [],
    attach: (o) => {
      typeof a.neoTooltip > "u" || c("neo.tooltip", ".use-neo-tooltip", o).forEach((t) => {
        const i = Object.assign({}, {
          theme: "neo",
          inertia: !0,
          allowHtml: !0
        }, a.neoTooltip), r = t.getAttribute("data-tippy-animation") || i.animation;
        if (r && ["shift-toward", "shift-away", "scale", "perspective"].includes(r) && p(r), t.getAttribute("data-tippy-trigger-nearest")) {
          const n = t.closest("a, input, button");
          if (n && n !== t)
            i.triggerTarget = [n];
          else {
            const e = t.closest("label");
            if (e)
              i.triggerTarget = [e];
            else {
              const l = t.closest("input");
              l && (i.triggerTarget = [l]);
            }
          }
        }
        t.getAttribute("data-tippy-template") && (i.content = (n) => {
          let e = n.nextElementSibling;
          return e && e.tagName === "TEMPLATE" || (e = n.querySelector(".neo-tooltip-template"), e && e.tagName === "TEMPLATE") ? e.innerHTML : "";
        }), i.onShow = (n) => n.props.content.length != 0, s.behaviors.neoTooltip.instances.push(tippy(t, i));
      });
    },
    disableAll: () => {
      s.behaviors.neoTooltip.instances.forEach((o) => {
        o.disable();
      });
    },
    enableAll: () => {
      s.behaviors.neoTooltip.instances.forEach((o) => {
        o.enable();
      });
    }
  };
})(Drupal, drupalSettings, once);
//# sourceMappingURL=tooltip.js.map
