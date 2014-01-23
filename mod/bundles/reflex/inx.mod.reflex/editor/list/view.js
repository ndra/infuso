// @link_with_parent
// @include inx.button

inx.mod.reflex.editor.list.view = inx.button.extend({

    constructor:function(p) {
        p.air = true;
        p.icon = "list";
        p.menu = [];
        for(var i in p.modes)
            p.menu.push({
                text:p.modes[i].title,
                icon:p.modes[i].icon,
                onclick:inx.cmd(this.id(),"changeView",p.modes[i].id)
            });
        
        if(p.onchange)
            this.on("change",p.onchange);
        this.base(p);
        inx.storage.onready(this.id(),"handleStorage");        
    },
    
    cmd_changeView:function(view) {    
        if(this.view==view) return;    
            
        inx.storage.set("n4lrjw9zdcu2"+this.itemClass,view);
        this.view = view;
        this.task("fireChange");
        this.cmd("setIcon",this.modes[view] ? this.modes[view].icon : "list");
    },
    
    cmd_fireChange:function() {
        this.fire("change");
    },
    
    cmd_handleStorage:function() {
        var view = inx.storage.get("n4lrjw9zdcu2"+this.itemClass);
        if(view) this.cmd("changeView",view);
    },
    
    info_value:function() {
        return this.view;
    }
    
});