// @link_with_parent
// @include inx.tabs

inx.mod.moduleManager.fileManager.editor = inx.tabs.extend({

    constructor:function(p) {
        p.title = p.path;
        p.height = parent;
        p.showHead = false;
        this.call({
            cmd:"moduleManager/fileManager/getContents",
            path:p.path
        },[this.id(),"handleContents"]);
        this.base(p);
    },
    
    cmd_handleContents:function(data) {

        this.editor = inx({
            type:"inx.code",
            value:data.code,
            height:"parent",
            lang:data.lang
        });
        
        this.cmd("add",this.editor);
        inx.hotkey("ctrl+s",[this.id(),"save"]);

    },
    
    cmd_save:function() {
    
        if(!this.editor) {
            return false;
        }
        
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