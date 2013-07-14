// @link_with_parent
// @include inx.tabs

inx.mod.reflex.editor.item.tabs = inx.tabs.extend({
    
    constructor:function(p) {
        p.selectNew = false;
        p.height = "parent";
        this.base(p);
        if(this.items().length()<2) {
            this.cmd("hideTabs");
        }
        this.on("show",this.id(),"handleShow");        
    },
    
    cmd_handleShow:function() {
        this.items().cmd("handleShow");
    }
    
});