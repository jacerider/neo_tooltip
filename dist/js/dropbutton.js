(function(i, c, b) {
  i.behaviors.neoTooltipDropbutton = {
    attach: (m) => {
      typeof c.neoTooltip > "u" || b("neo.tooltip.dropbutton", ".dropbutton", m).forEach((t) => {
        var l, u;
        const r = t.querySelector("li");
        if (!r) {
          console.error("No li elements found in the list");
          return;
        }
        t.classList.add("neo-dropbutton"), r.classList.add("dropbutton-action");
        const d = document.createElement("ul"), n = t.querySelectorAll("li");
        if (n.length <= 1) {
          t.classList.add("dropbutton-single");
          return;
        }
        t.classList.add("dropbutton-multiple");
        for (let o = 1; o < n.length; o++) {
          const p = n[o].cloneNode(!0);
          (l = p.querySelector("a")) == null || l.removeAttribute("data-once"), d.appendChild(p), n[o].classList.add("sr-only"), (u = n[o].querySelector("a")) == null || u.removeAttribute("id");
        }
        const s = document.createElement("li");
        s.classList.add("dropbutton-toggle");
        const a = document.createElement("button");
        a.setAttribute("type", "button"), a.innerHTML = '<span class="dropbutton-arrow"><span class="visually-hidden">' + i.t("List additional actions") + "</span></span>", s.appendChild(a), t.insertBefore(s, r.nextSibling);
        const e = {};
        e.content = d, e.theme = "neo dropbutton", e.trigger = "click", e.interactive = !0, e.placement = "bottom-end", e.onShow = (o) => {
          i.attachBehaviors(o.popper, c), t.classList.add("is-active");
        }, e.onHide = (o) => {
          t.classList.remove("is-active");
        }, i.behaviors.neoTooltip.addInstance(s, e);
      });
    }
  };
})(Drupal, drupalSettings, once);
//# sourceMappingURL=dropbutton.js.map
