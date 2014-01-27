// @include inx.panel

inx.ns("inx.mod.inxdev.example").panel = inx.panel.extend({

    constructor:function(p) {
        p.autoHeight = 1;
        p.width = 300;
        var cmpid = this.id();
        p.tbar = [
            {type:"inx.textfield",width:100,onchange:function() {
                inx(cmpid).cmd("setPadding",this.info("value"));
            }}
        ];
        this.base(p);
        for(var i=0;i<3;i++)
            this.cmd("add",{html:"subpanel",autoHeight:true});
    }

});