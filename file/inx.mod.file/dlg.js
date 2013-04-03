// @include inx.dialog

inx.ns("inx.mod.file").dlg = inx.dialog.extend({

    constructor:function(p) {
        p.width = 800;
        p.layout = "inx.layout.fit";
        p.title = "Выберите файл";
        
        this.filemanager = inx({
            type:"inx.mod.file.manager",
            style:{
                maxHeight:400
            },
            storage:p.storage,
            disableAutoOpen:true
        });
        
        this.filemanager.on("fileSelected",[this.id(),"handleSelect"]);        
        p.items = [this.filemanager];
        
        p.autoDestroy = true;
        
        this.base(p);
    },
    
    cmd_handleSelect:function(url) {
        this.fire("select",url);
        this.cmd("destroy");
    }

})