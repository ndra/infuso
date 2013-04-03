// @include inx.panel

inx.ns("inx.mod.inxdev.example").textarea = inx.panel.extend({

    constructor:function(p) {
        p.width = 800;
        this.textarea = inx({
            type:"inx.textarea",
            value:'sdf asdfsjdbf jdsbf jhdbsfj bsdjbf adsbj'
        });
        p.items = [this.textarea];
        p.tbar = [
            {text:"&lt;b&gt;",onclick:[this.id(),"bold"]},
            {type:"inx.textarea",width:200}
        ]
        this.base(p);
    },
    
    cmd_bold:function() {    
        this.textarea.cmd("embraceSelection","<b>","</b>");
    }

});