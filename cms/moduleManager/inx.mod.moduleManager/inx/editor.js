// @include inx.tree,inx.code

inx.ns("inx.mod.moduleManager.inx").editor = inx.tabs.extend({

    constructor:function(p) {
        this.stateParams = {
            path:p.path,
            type:p.type,
            module:p.module
        };
        if(!p) p = {};
        p.title = p.path.split("/").join(".");
        p.showHead = false;
        this.call(
            {cmd:"moduleManager_inxManager:getContents",module:p.module,path:p.path},
            [this.id(),"handleContents"]
        );
        this.base(p);
    },
    
    cmd_handleContents:function(data) {
        this.editorPHP =inx({type:"inx.code",value:data.php,title:"PHP",lang:"js"});
        this.cmd("add",this.editorPHP);
        inx.hotkey("ctrl+s",[this.id(),"save"]);
    },
    
    cmd_save:function() {
        if(!this.editorPHP) return;
        this.call({
            cmd:"moduleManager_inxManager:setContents",
            path:this.path,
            module:this.module,
            code:this.editorPHP.info("value")
        });
        return false;
    },
    
    cmd_updateParams:function(p) {
        this.path = p.path;
        this.name = "inx:"+p.path;
        this.cmd("setTitle",p.path);
    }

})