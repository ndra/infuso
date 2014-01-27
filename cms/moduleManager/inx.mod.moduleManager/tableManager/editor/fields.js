// @link_with_parent
// @include inx.list

inx.mod.moduleManager.tableManager.editor.fields = inx.list.extend({

    constructor:function(p) {    
        p.moreFieldName = "e0n8azd4ai3w9btmeixc";
        p.tbar = [            
            {icon:"plus",text:"Добавить поле",onclick:[this.id(),"addField"]},
            {icon:"delete",onclick:[this.id(),"deleteField"]},
            "|",
            {icon:"up",onclick:[this.id(),"upField"]},
            {icon:"down",onclick:[this.id(),"downField"]},
            "|",
            {icon:"refresh",onclick:[this.id(),"load"]},"|",
            {text:"Настройки",onclick:[this.id(),"showConf"]}
        ];
        p.loader = {
            cmd:"moduleManager:tableManager:describeTable",
            tableID:p.tableID,
            module:p.module
        }
        p.sortable = true;
        this.base(p);
        this.on("itemdblclick","editField");
        this.on("load","handleLoad");
    },
    
    cmd_handleLoad:function(data) {
        this.owner().cmd("setTitle",data.name);
    },
    
    cmd_showConf:function() {
        inx({
            type:"inx.mod.moduleManager.tableManager.editor.conf",
            tableID:this.tableID,
            listeners:{"change":[this.id(),"load"]}
        }).cmd("render");
    },
    
    cmd_addField:function() {
        this.call({cmd:"moduleManager:tableManager:addField",tableID:this.tableID},[this.id(),"load"]);
    },
    
    cmd_deleteField:function() {
        var ids = this.info("selection");
        if(!confirm("Удалить выбранные поля?")) return;
        this.call({cmd:"moduleManager:tableManager:deleteField",tableID:this.tableID,ids:ids},[this.id(),"load"]);
    },
    
    cmd_editField:function(id) {
    
        var item = this.info("item",id);
        
        if(item.isGroup) {    
        
            inx({
                type:"inx.mod.moduleManager.tableManager.group",
                tableID:this.tableID,
                groupID:id,
                listeners:{save:[this.id(),"load"]}
            }).cmd("render");
        
        } else {   
            inx({
                type:"inx.mod.moduleManager.tableManager.field",
                tableID:this.tableID,
                fieldID:id,
                listeners:{save:[this.id(),"load"]}
            }).cmd("render");
        }
    },
    
    cmd_upField:function() {
        var id = this.info("selection")[0];
        if(!id) return;
        this.call({cmd:"moduleManager:tableManager:upField",tableID:this.tableID,fieldID:id},[this.id(),"load"]);
    },
    
    cmd_downField:function() {
        var id = this.info("selection")[0];
        if(!id) return;
        this.call({cmd:"moduleManager:tableManager:downField",tableID:this.tableID,fieldID:id},[this.id(),"load"]);
    }

});