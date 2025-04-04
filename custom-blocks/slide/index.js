(() => {
    "use strict";
    const e = window.wp.blocks,
          t = window.React,
          n = window.wp.blockEditor,
          l = window.wp.data;

    const o = (0, t.createElement)(
        "svg",
        {
            width: "24",
            height: "24",
            xmlns: "http://www.w3.org/2000/svg",
            role: "img",
            "aria-hidden": "true",
            focusable: "false"
        },
        " ",
        (0, t.createElement)("path", { d: "M0 0h24v24H0z", fill: "none" }),
        " ",
        (0, t.createElement)("path", { d: "m18 6v11h4v-11h-4z" }),
        " ",
        (0, t.createElement)("path", { d: "m2 17h4v-11h-4v11z" }),
        " ",
        (0, t.createElement)("path", { d: "m7.0059 4v15.004h9.9902v-15.004h-9.9902zm1.3574 1.3555h7.2773v12.291h-7.2773v-12.291z" })
    );

    (0, e.registerBlockType)("cb/slide", {
        icon: o,
        edit: function({ clientId: e }) {
            const o = (0, n.useBlockProps)(),
                  { hasChildBlocks: c } = (0, l.useSelect)((t => {
                      const { getBlockOrder: l } = t(n.store);
                      return { hasChildBlocks: l(e).length > 0 };
                  }), [e]);

            return (0, t.createElement)(
                "div",
                { ...o },
                (0, t.createElement)(n.InnerBlocks, {
                    templateLock: !1,
                    renderAppender: c ? void 0 : n.InnerBlocks.ButtonBlockAppender
                })
            );
        },
        save: function() {
            const e = n.useBlockProps.save();
            return (0, t.createElement)(
                "div",
                { ...e },
                (0, t.createElement)(n.InnerBlocks.Content, null)
            );
        }
    });
})();