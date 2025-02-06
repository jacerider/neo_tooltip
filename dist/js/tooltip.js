(function(l, r, p) {
  const c = function(i) {
    let t = document.querySelector(`link[neo-tooltip-animation-${i}]`);
    t || (t = document.createElement("link"), t.rel = "stylesheet", t.type = "text/css", t.media = "screen", t.href = "/" + r.neoTooltip.dir + `/dist/css/tippy-${i}.css`, t.setAttribute(`neo-tooltip-animation-${i}`, ""), document.getElementsByTagName("head")[0].appendChild(t));
  };
  l.behaviors.neoTooltip = {
    attach: (i) => {
      typeof r.neoTooltip > "u" || p("neo.tooltip", ".use-neo-tooltip", i).forEach((t) => {
        const n = Object.assign({}, {
          theme: "neo",
          inertia: !0,
          allowHtml: !0
        }, r.neoTooltip), s = t.getAttribute("data-tippy-animation") || n.animation;
        if (s && ["shift-toward", "shift-away", "scale", "perspective"].includes(s) && c(s), t.getAttribute("data-tippy-trigger-nearest")) {
          const o = t.closest("a, input, button");
          if (o && o !== t)
            n.triggerTarget = [o];
          else {
            const e = t.closest("label");
            if (e)
              n.triggerTarget = [e];
            else {
              const a = t.closest("input");
              a && (n.triggerTarget = [a]);
            }
          }
        }
        t.getAttribute("data-tippy-template") && (n.content = (o) => {
          let e = o.nextElementSibling;
          return e && e.tagName === "TEMPLATE" || (e = o.querySelector(".neo-tooltip-template"), e && e.tagName === "TEMPLATE") ? e.innerHTML : "";
        }), n.onShow = (o) => o.props.content.length != 0, tippy(t, n);
      });
    }
  };
})(Drupal, drupalSettings, once);
//# sourceMappingURL=tooltip.js.map
