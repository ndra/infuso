// @link_with_parent
inx.mod.moduleManager.templateManager.editor.tab = inx.tabs.extend({

    constructor:function(p) {
        p.showHead = false;
        this.base(p);
        this.call({
            cmd:"moduleManager_templateManager:getContents",
            id:this.templateID,
            themeID:this.themeID,
            contentType:this.contentType
        },[this.id(),"handleContents"]);
    },
    
    cmd_handleContents:function(data) {
        this.editorPHP = inx({
            type:"inx.mod.moduleManager.advcode",
            value:data.code,
            comments:data.comments,
            lang:"php",
            tbar:[
                {text:"Сохранить",icon:"save",onclick:[this.id(),"save"]},
                {text:"Восстановить",onclick:[this.id(),"restore"]},
                "|",
                {text:"Дополнительно",icon:"gear",onclick:[this.id(),"showPrefs"]}
            ]
        });
        this.cmd("add",this.editorPHP);
        inx.hotkey("ctrl+s",[this.id(),"save"]);
    },
    
    cmd_save:function() {
        if(!this.editorPHP) return;
        this.call({
            cmd:"moduleManager_templateManager:setContents",
            id:this.templateID,
            themeID:this.themeID,
            code:this.editorPHP.info("value"),
            contentType:this.contentType
        });
        return false;
    },
    
    cmd_showPrefs:function() {
        inx({
            type:"inx.mod.moduleManager.templateManager.editor.preferences",
            templateID:this.templateID,
        }).setOwner(this).cmd("render");
    }

})