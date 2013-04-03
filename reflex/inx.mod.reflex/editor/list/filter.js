// @link_with_parent

inx.mod.reflex.editor.list.filter = inx.panel.extend({

    constructor:function(p) {
        p.width = 440;
        if(!p.style)
            p.style = {}
        p.style.padding = 20;
        p.style.vscroll = true;
        p.resizable = true;
        p.name = "filters";
        this.base(p);
        this.task("check");
    },    
   
    cmd_check:function() {
    
        var fn = inx.cmd(this,"check");
        setTimeout(fn,300);
        
        if(this.info("hidden"))
            return;
        
        var data = this.info("data");
        var hash = inx.json.encode(data);
        if(hash!=this.lastHash) {
            this.task("change");
            this.lastHash = hash;
        }
    },

    cmd_change:function() {
        this.fire("change");
    }
    
});