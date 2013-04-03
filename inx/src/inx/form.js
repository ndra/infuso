// @include inx.panel

inx.form = inx.panel.extend({

    constructor:function(p) {
    
        if(!p.style) {
            p.style = {};
        }
    
        if(!p.style.spacing) {
            p.style.spacing = 10;
        }
        
        if(!p.style.padding) {
            p.style.padding = 10;
        }
    
        if(p.labelWidth===undefined)
            p.labelWidth = 150;
        p.layout = "inx.layout.form";
        this.base(p);

    },

    __defaultChildType:"inx.textfield",
    
    cmd_setData:function(data) {
        if(!data) return;
        for(var i=0;i<this.private_items.length;i++) {
            var cmp = inx(this.private_items[i]);
            var name = cmp.info("name");
            var val = data[name];
            if(val!==undefined)
                cmp.cmd("setValue",val)
        }
    }
    
});
