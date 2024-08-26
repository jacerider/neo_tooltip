(function(l, r, p) {
  const c = function(i) {
    let t = document.querySelector(`link[neo-tooltip-animation-${i}]`);
    t || (t = document.createElement("link"), t.rel = "stylesheet", t.type = "text/css", t.media = "screen", t.href = "/" + r.neoTooltip.dir + `/dist/css/tippy-${i}.css`, t.setAttribute(`neo-tooltip-animation-${i}`, ""), document.getElementsByTagName("head")[0].appendChild(t));
  };
  l.behaviors.neoTooltip = {
    attach: (i) => {
      typeof r.neoTooltip > "u" || p("neo.tooltip", ".use-neo-tooltip", i).forEach((t) => {
        const o = Object.assign({}, {
          theme: "neo",
          inertia: !0
        }, r.neoTooltip), s = t.getAttribute("data-tippy-animation") || o.animation;
        if (s && ["shift-toward", "shift-away", "scale", "perspective"].includes(s) && c(s), t.getAttribute("data-tippy-trigger-nearest")) {
          const n = t.closest("a, input, button");
          if (n)
            o.triggerTarget = n;
          else {
            const e = [], a = t.closest(".form-item");
            a && (e.push(a.querySelector("input")), e.push(a.querySelector("label"))), e.length && (o.triggerTarget = e);
          }
        }
        t.getAttribute("data-tippy-template") && (o.allowHTML = !0, o.content = (n) => {
          let e = n.nextElementSibling;
          return e && e.tagName === "TEMPLATE" || (e = n.querySelector(".neo-tooltip-template"), e && e.tagName === "TEMPLATE") ? e.innerHTML : "";
        }), tippy(t, o);
      });
    }
  };
})(Drupal, drupalSettings, once);
//# sourceMappingURL=tooltip.js.map
