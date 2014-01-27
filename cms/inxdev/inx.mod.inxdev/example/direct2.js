// @include inx.list,inx.direct

inx.ns("inx.mod.inxdev.example").direct2 = inx.panel.extend({

    constructor:function(p) {
        this.text = inx({
            type:"inx.textfield",
            onchange:[this.id(),"handleChange"]
        });
        p.autoHeight = true;
        p.items = [this.text];
        this.base(p);
        inx.direct.bind(this,"handleDirect");
    },
    
    cmd_handleChange:function() {
        inx.direct.set(this.text.info("value"));        
    },
    
    cmd_handleDirect:function(p1) {
        this.text.cmd("setValue",p1);
    }

});