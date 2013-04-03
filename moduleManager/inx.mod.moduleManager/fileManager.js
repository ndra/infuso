inx.ns("inx.mod.moduleManager").fileManager = inx.panel.extend({

    constructor:function(p) {
        p.layout = "inx.layout.fit";
        p.items = [{
            type:"inx.mod.moduleManager.fileManager.folder",
            module:p.module,
            viewMode:"tree",
            style:{
                height:"parent"
            }
        }];
        this.base(p);
    },
    
    cmd_keydown:function(e) {
        if(e.keyCode==46) {
            this.cmd("deleteFile");
            return;
        }
        this.base(e);
    }
    
})
