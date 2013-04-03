// @link_with_parent
// @include inx.tabs

inx.mod.moduleManager.fileManager.editor = inx.tabs.extend({

    constructor:function(p) {
        p.title = p.module+":"+p.path;
        p.height = parent;
        p.showHead = false;
        this.call({cmd:"moduleManager_fileManager:getContents",module:p.module,path:p.path},[this.id(),"handleContents"]);
        this.base(p);
    },
    
    cmd_handleContents:function(data) {
        switch(data.type) {
            case "code":
                this.editor = inx({
                    type:"inx.code",
                    value:data.code,
                    height:"parent",
                    lang:data.lang
                });
                this.cmd("add",this.editor);
                inx.hotkey("ctrl+s",this,"save");
                break;
            case "folder":
                this.editor = inx({
                    type:"inx.mod.moduleManager.fileManager.folder",
                    path:this.path,
                    module:this.module
                });
                this.cmd("add",this.editor);
                break;
        }
    },
    
    cmd_save:function() {
        if(!this.editor) return false;
        this.call({
            cmd:"moduleManager_fileManager:setContents",
            path:this.path,
            module:this.module,
            php:this.editor.info("value"),
        });
        return false;
    },
    
    cmd_updateParams:function(p) {
        this.path = p.path;
        this.name = "file:"+p.path;
        this.cmd("setTitle",p.path);
    }
})