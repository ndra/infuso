inx.checkbox = inx.box.extend({

    constructor:function(p) {
    
        if(!p.style) {
            p.style = {}
        }
        
        if(!p.border) {
            p.border = 0;
        }
        
        if(!p.background) {
            p.background = "none";
        }
        
        if(!p.height) {
            p.height = 18;
        }
        
        if(!p.width) {
            p.width = "content";
        }

        this.private_label = p.label;
        p.label = null;
        if(!p.labelAlign)
            p.labelAlign = "left";
        p.value = this.private_boolval(p.value);
        this.base(p);
        if(p.onchange)
            this.on("change",p.onchange);
    },
    
    cmd_render:function(c) {
        this.base(c);
        this.input = $("<input style='cursor:pointer' type='checkbox' />").appendTo(this.el);
        var html = this.private_label || "";
        this.labelContainer = $("<div>").click(inx.cmd(this,"toggle")).css({cursor:"pointer",whiteSpace:"nowrap",position:"absolute",left:22,top:2}).html(html).appendTo(this.el);
        var id = this.id();
        this.input.change(function(){inx(id).cmd("handleChangesNative")});
        this.cmd("setValue",this.value);
        this.cmd("autoWidth");
    },
    
    cmd_autoWidth:function() {
        var a = $("<div/>").addClass("inx-box").appendTo("body");
        var inp = this.labelContainer.clone().appendTo(a);
        this.cmd("widthContent",inp.get(0).clientWidth+22);
        a.remove();
    },
    
    cmd_toggle:function() {
        this.cmd("setValue",!this.info("value"));
        this.fire("change",this.info("value"))
    },
    
    cmd_handleChangesNative:function() {
        this.fire("change",this.info("value"))
    },
    
    private_boolval:function(val) {
        if(val==="0") return 0;
        return val ? 1 : 0;
    },
    
    info_value:function() {
        if(this.input) {
            return this.input.prop("checked") ? 1 : 0;
        } else {
            return this.value ? 1 : 0        
        }
    },
    
    cmd_setValue:function(val) {
        val = this.private_boolval(val);
        this.private_value = val;
        if(this.input) this.input.prop("checked",val ? "on" : "");
    }

})
