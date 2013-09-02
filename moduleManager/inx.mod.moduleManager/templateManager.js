// @include inx.tabs,inx.tree,inx.code

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
