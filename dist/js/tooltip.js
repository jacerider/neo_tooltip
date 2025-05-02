(function(i, n, p) {
  const h = function(t) {
    let e = document.querySelector(`link[neo-tooltip-animation-${t}]`);
    e || (e = document.createElement("link"), e.rel = "stylesheet", e.type = "text/css", e.media = "screen", e.href = "/" + n.neoTooltip.dir + `/dist/css/tippy-${t}.css`, e.setAttribute(`neo-tooltip-animation-${t}`, ""), document.getElementsByTagName("head")[0].appendChild(e));
  };
  i.behaviors.neoTooltip = {
    instances: [],
    attach: (t) => {
      i.behaviors.neoTooltip.hideAll(), !(typeof n.neoTooltip > "u") && p("neo.tooltip", ".use-neo-tooltip", t).forEach((e) => {
        const o = Object.assign({}, {
          theme: "neo",
          inertia: !0
        }, n.neoTooltip), a = e.getAttribute("data-tippy-animation") || o.animation;
        if (a && ["shift-toward", "shift-away", "scale", "perspective"].includes(a) && h(a), e.getAttribute("data-tippy-trigger-nearest")) {
          const s = e.closest("a, input, button");
          if (s && s !== e)
            o.triggerTarget = [s];
          else {
            const r = e.closest("label");
            if (r)
              o.triggerTarget = [r];
            else {
              const c = e.closest("input");
              c && (o.triggerTarget = [c]);
            }
          }
        }
        const l = e.getAttribute("data-tippy-template");
        l && n.neoTooltipTemplates && n.neoTooltipTemplates[l] && (o.allowHTML = !0, o.interactive = !0, o.content = n.neoTooltipTemplates[l]), o.onShow = (s) => s.props.content.length != 0, i.behaviors.neoTooltip.instances.push(tippy(e, o));
      });
    },
    hideAll: () => {
      i.behaviors.neoTooltip.instances.forEach((t) => {
        t.hide();
      });
    },
    disableAll: () => {
      i.behaviors.neoTooltip.instances.forEach((t) => {
        t.disable();
      });
    },
    enableAll: () => {
      i.behaviors.neoTooltip.instances.forEach((t) => {
        t.enable();
      });
    }
  };
})(Drupal, drupalSettings, once);
//# sourceMappingURL=tooltip.js.map
