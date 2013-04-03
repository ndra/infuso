// @include inx.select

inx.ns("inx.mod.reflex.fields").readonly = inx.panel.extend({

    constructor:function(p) {
        p.autoHeight = true;
        p.labelAlign = "left";
        p.style = {
            border:0,
            background:"none"
        }
        this.base(p);
        this.cmd("setValue",p.value);
    },
    
    cmd_setValue:function(val) {
        this.cmd("html",val);
    }

});