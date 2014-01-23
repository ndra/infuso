// @include inx.tabs,inx.tree,inx.code
/*-- /moduleManager/inx.mod.moduleManager/templateManager.js --*/


inx.ns("inx.mod.moduleManager");
inx.mod.moduleManager.templateManager = inx.tree.extend({

    constructor:function(p) {
        p.root = {text:"Шаблоны",id:p.module};
        p.tbar = [
            {icon:"plus",help:"Добавить шаблон",onclick:[this.id(),"newTemplate"]},
            {icon:"delete",help:"Удалить шаблон",onclick:[this.id(),"deleteTemplate"]},
            {icon:"refresh",help:"Обновить",onclick:[this.id(),"reloadTemplates"]},
            {icon:"wand",help:"Собрать старые шаблоны сюда",onclick:[this.id(),"restoreTemplates"]}     
        ];
        p.loader = {cmd:"moduleManager_templateManager:listTemplates"};        
        p.editKey = "name";
        
        if(!p.listeners) p.listeners = {};
        p.listeners.dblclick = [this.id(),"selectionChange"];
        p.listeners.editComplete = [this.id(),"handleRename"];
        
        this.on("beforeload",[this.id(),"beforeLoad"]);
        
        this.base(p);
    },
    
    cmd_beforeLoad:function(p) {
        p.themeID = this.themeID;
    },
    
    cmd_selectionChange:function() {    
        var sel = this.info("selection")[0];    
        this.fire("openEditor",{
            type:"inx.mod.moduleManager.templateManager.editor",
            templateID:sel,
            themeID:this.themeID,
            name:this.themeID+":"+sel,
            closable:true
        });
    },
    
    cmd_newTemplate:function() {    
        var name = prompt("Имя шаблона");
        if(!name) return;
        var sel = this.info("selection")[0];
        this.call({
            cmd:"moduleManager_templateManager:newTemplate",
            id:sel,
            themeID:this.themeID,
            module:this.module,
            name:name
        },[this.id(),"load"]);
    },
    
    cmd_handleNewTemplate:function(data,meta) {
        this.cmd("load",meta.parent);
    },
    
    cmd_deleteTemplate:function() {
        if(!confirm("Удалить шаблон?")) return;
        var sel = this.info("selection")[0];
        this.call({
            cmd:"moduleManager_templateManager:deleteTemplate",
            id:sel,
            themeID:this.themeID,
            module:this.module
        },[this.id(),"load"]);
    },
    
    cmd_handleRename:function(id,name) {
        this.call({
            cmd:"moduleManager:templateManager:renameTemplate",
            themeID:this.themeID,
            id:id,
            name:name
        },[this.id(),"load"]);
    },
    
    cmd_reloadTemplates:function() {
        var sel = this.info("selection")[0];
        this.cmd("load",sel);
    },
    
    cmd_restoreTemplates:function() {
    
        var n = Math.round(Math.random()*10000)+1;
        if(window.prompt("Введите "+n)!=n) {
            return;
        }
    
        this.call({
            cmd:"moduleManager:templateManager:restoreTemplates",
            themeID:this.themeID
        },[this.id(),"load"]);   
    }
    
})


/*-- /moduleManager/inx.mod.moduleManager/templateManager/editor.js --*/

inx.mod.moduleManager.templateManager.editor = inx.tabs.extend({

    constructor:function(p) {
        p.title = p.templateID;
        p.selectNew = false;
        this.base(p);        
        var cc = ["php","JS","Css"];
        for(var i in cc)
            this.cmd("add",{
                type:"inx.mod.moduleManager.templateManager.editor.tab",
                templateID:this.templateID,
                themeID:this.themeID,
                contentType:cc[i],
                title:cc[i],
                lazy:true
            });
    }

})

/*-- /moduleManager/inx.mod.moduleManager/templateManager/editor/tab.js --*/

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

