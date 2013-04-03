// @include inx.wysiwyg

inx.ns("inx.mod.inxdev.example").wysiwyg = inx.wysiwyg.extend({

    constructor:function(p) {
        p.height = 400;
        this.code = inx({
            type:"inx.textarea",
            region:"bottom",
            height:200,
            resizable:true
        })
        p.side = [
            this.code
        ]
        
        p.value = "Превед";
        
        this.base(p);
        setInterval(inx.cmd(this,"syncCode"),1000)
    },
    
    cmd_syncCode:function() {
        var val = this.info("value")
        this.code.cmd("setValue",val);
    }

});