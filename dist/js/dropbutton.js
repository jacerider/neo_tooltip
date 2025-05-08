(function(n, c, p) {
  n.behaviors.neoTooltipDropbutton = {
    attach: (b) => {
      typeof c.neoTooltip > "u" || p("neo.tooltip.dropbutton", ".dropbutton", b).forEach((t) => {
        var l;
        const r = t.querySelector("li");
        if (!r) {
          console.error("No li elements found in the list");
          return;
        }
        t.classList.add("neo-dropbutton"), r.classList.add("dropbutton-action");
        const d = document.createElement("ul"), i = t.querySelectorAll("li");
        if (i.length <= 1) {
          t.classList.add("dropbutton-single");
          return;
        }
        t.classList.add("dropbutton-multiple");
        for (let o = 1; o < i.length; o++) {
          const u = i[o].cloneNode(!0);
          (l = u.querySelector("a")) == null || l.removeAttribute("data-once"), d.appendChild(u), i[o].classList.add("sr-only");
        }
        const s = document.createElement("li");
        s.classList.add("dropbutton-toggle");
        const a = document.createElement("button");
        a.setAttribute("type", "button"), a.innerHTML = '<span class="dropbutton-arrow"><span class="visually-hidden">' + n.t("List additional actions") + "</span></span>", s.appendChild(a), t.insertBefore(s, r.nextSibling);
        const e = {};
        e.content = d, e.theme = "neo dropbutton", e.trigger = "click", e.interactive = !0, e.placement = "bottom-end", e.onShow = (o) => {
          n.attachBehaviors(o.popper, c), t.classList.add("is-active");
        }, e.onHide = (o) => {
          t.classList.remove("is-active");
        }, n.behaviors.neoTooltip.addInstance(s, e);
      });
    }
  };
})(Drupal, drupalSettings, once);
//# sourceMappingURL=dropbutton.js.map
