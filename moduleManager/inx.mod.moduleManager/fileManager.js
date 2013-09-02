inx.ns("inx.mod.moduleManager").fileManager = inx.panel.extend({

    constructor:function(p) {
        p.layout = "inx.layout.fit";
        p.items = [{
            type:"inx.mod.moduleManager.fileManager.folder",
            basedir:p.basedir,
            viewMode:"tree",
            style:{
                height:"parent"
            }
        }];
        this.base(p);
    }
    
})
