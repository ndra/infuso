// @link_with_parent
// @include inx.panel

inx.mod.moduleManager.fileManager.folder.path = inx.panel.extend({

    constructor:function(p) {
        //p.html = 666;
        p.autoHeight = true;
        p.background = "#ededed";
        p.padding = 4;
        this.base(p);
    },
    
    cmd_setPath:function() {
    }

})