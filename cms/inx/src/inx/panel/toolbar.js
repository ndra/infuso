// @link_with_parent

inx.panel.toolbar = inx.panel.extend({

    constructor:function(p) {
        p.style = {
            background:"#ededed",
            padding:5,
            spacing:2,
            height:"content",
        }
        p.layout = "inx.layout.column";
        this.base(p);
    },

    __defaultChildType:"inx.button",
      
    cmd_add:function(c) {
        if(c=="|")
            c={ type:"inx.panel.separator" };
        this.base(c);
    }
    
});

inx.toolbar = inx.panel.toolbar;

