(function(l, s, c) {
  const p = function(n) {
    let t = document.querySelector(`link[neo-tooltip-animation-${n}]`);
    t || (t = document.createElement("link"), t.rel = "stylesheet", t.type = "text/css", t.media = "screen", t.href = "/" + s.neoTooltip.dir + `/dist/css/tippy-${n}.css`, t.setAttribute(`neo-tooltip-animation-${n}`, ""), document.getElementsByTagName("head")[0].appendChild(t));
  };
  l.behaviors.neoTooltip = {
    attach: (n) => {
      typeof s.neoTooltip > "u" || c("neo.tooltip", ".use-neo-tooltip", n).forEach((t) => {
        const i = Object.assign({}, {
          theme: "neo",
          inertia: !0
        }, s.neoTooltip), a = t.getAttribute("data-tippy-animation") || i.animation;
        if (a && ["shift-toward", "shift-away", "scale", "perspective"].includes(a) && p(a), t.getAttribute("data-tippy-trigger-nearest")) {
          const o = t.closest("a, input, button");
          if (o && o !== t)
            i.triggerTarget = [o];
          else {
            const e = t.closest("label");
            if (e)
              i.triggerTarget = [e];
            else {
              const r = t.closest("input");
              r && (i.triggerTarget = [r]);
            }
          }
        }
        t.getAttribute("data-tippy-template") && (i.allowHTML = !0, i.content = (o) => {
          let e = o.nextElementSibling;
          return e && e.tagName === "TEMPLATE" || (e = o.querySelector(".neo-tooltip-template"), e && e.tagName === "TEMPLATE") ? e.innerHTML : "";
        }), tippy(t, i);
      });
    }
  };
})(Drupal, drupalSettings, once);
//# sourceMappingURL=tooltip.js.map
