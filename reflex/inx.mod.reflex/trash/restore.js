// @include inx.dialog

inx.ns("inx.mod.reflex.trash").restore = inx.button.extend({

    constructor:function(p) {
        p.text = "Восстановить";
        p.icon = "wand";
        p.onclick = [this.id(),"restore"];
        this.base(p);
    },
    
    cmd_restore:function() {
        this.call({cmd:"reflex:pluginRestore:restore",ids:this.ids},[this.id(),"handleRestore"]);
    },
    
    cmd_handleRestore:function() {
        this.fire("update");
    },
    
    cmd_select:function(ids) {
        this.ids = ids;
    }
     
});