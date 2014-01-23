// @include inx.list,inx.form,inx.select,inx.tabs,inx.dialog
/*-- /moduleManager/inx.mod.moduleManager/tableManager.js --*/


inx.ns("inx.mod.cat");
inx.mod.moduleManager.tableManager = inx.list.extend({

    constructor:function(p) {
      
        p.loader = {
            cmd:"moduleManager/tableManager/listTables",
            module:p.module
        };
        
        p.tbar = [
            {icon:"plus",onclick:[this.id(),"addTable"]},
            {icon:"delete",onclick:[this.id(),"deleteTable"]},
            {icon:"refresh",onclick:[this.id(),"load"]}
        ];      
        
        if(!p.listeners) {
            p.listeners = {};
        }
        
        p.listeners.selectionchange = [this.id(),"handleSelectionChange"];
        this.base(p);
    },
    
    cmd_addTable:function() {
        this.call({
            cmd:"moduleManager/tableManager/addTable",
            module:this.module
        },[this.id(),"load"]);
    },
    
    cmd_deleteTable:function() {
    
        var id = this.info("selection")[0];
        
        if(!id) {
            return;
        }
        
        if(!confirm("Удалить таблицу?")) {
            return false;        
        }
        
        this.call({
            cmd:"moduleManager/tableManager/deleteTable",
            id:id,
            module:this.module
        },[this.id(),"load"]);
    },    
    
    cmd_handleSelectionChange:function(id) {
        id = id[0];
        this.fire("openEditor",{
            type:"inx.mod.moduleManager.tableManager.editor",
            tableID:id,
            name:"table:"+id,
            module:this.module
        });
    }

});


/*-- /moduleManager/inx.mod.moduleManager/tableManager/editor.js --*/


inx.mod.moduleManager.tableManager.editor = inx.tabs.extend({

    constructor:function(p) {    
    
        p.selectNew = false;
    
        p.items = [{
            title:"Поля",
            type:"inx.mod.moduleManager.tableManager.editor.fields",
            tableID:p.tableID,
            lazy:true
        },{
            title:"Индекс",
            type:"inx.mod.moduleManager.tableManager.editor.indexes",
            tableID:p.tableID,
            lazy:true
        }];
        
        this.base(p);
    }

});

/*-- /moduleManager/inx.mod.moduleManager/tableManager/editor/conf.js --*/


inx.mod.moduleManager.tableManager.editor.conf = inx.dialog.extend({

    constructor:function(p) {
        p.width = 500;
        this.form = inx({type:"inx.form"});
        p.items = [this.form];
        this.base(p);
        this.cmd("requestData");
    },
    
    cmd_requestData:function() {
        this.call({cmd:"moduleManager:tableManager:getTableConf",tableID:this.tableID},[this.id(),"handleData"]);
    },
    
    cmd_handleData:function(data) {
        this.form.cmd("add",{label:"Имя",value:data.name,name:"name"});
        this.form.cmd("add",{label:"Родительская таблица",value:data.parent,name:"parent"});
        this.form.cmd("add",{type:"inx.button",text:"Сохранить",onclick:[this.id(),"save"]});
    },
    
    cmd_save:function() {
        this.call({cmd:"moduleManager:tableManager:saveTableConf",tableID:this.tableID,data:this.info("data")},[this.id(),"handleSave"]);
    },
    
    cmd_handleSave:function() {
        this.fire("change");
        this.task("destroy");
    }
    
});

/*-- /moduleManager/inx.mod.moduleManager/tableManager/editor/fields.js --*/


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

/*-- /moduleManager/inx.mod.moduleManager/tableManager/editor/indexes.js --*/


inx.mod.moduleManager.tableManager.editor.indexes = inx.list.extend({

    constructor:function(p) {    
        p.tbar = [            
            {icon:"plus",text:"Добавить индекс",onclick:[this.id(),"addIndex"]},
            {icon:"plus",text:"Добавить полный индекс",onclick:[this.id(),"addFullIndex"]},
            {icon:"delete",onclick:[this.id(),"deleteIndex"]},
            "|",
            {icon:"refresh",onclick:[this.id(),"load"]}
        ];
        p.loader = {
            cmd:"moduleManager:tableManager:listIndexes",
            tableID:p.tableID,
        }
        this.base(p);
        this.on("itemdblclick","editIndex");
    },
    
    cmd_addIndex:function() {
        this.call({
            cmd:"moduleManager:tableManager:addIndex",
            tableID:this.tableID
        },[this.id(),"load"]);
    },
    
    cmd_addFullIndex:function() {
        this.call({
            cmd:"moduleManager:tableManager:addFullIndex",
            tableID:this.tableID
        },[this.id(),"load"]);
    },
    
    cmd_deleteIndex:function() {
        var ids = this.info("selection");
        if(!confirm("Удалить выбранные индексы?")) return;
        this.call({cmd:"moduleManager:tableManager:deleteIndex",tableID:this.tableID,ids:ids},[this.id(),"load"]);
    },
    
    cmd_editIndex:function(id) {
        inx({
            type:"inx.mod.moduleManager.tableManager.index",
            tableID:this.tableID,
            indexID:id,
            listeners:{save:[this.id(),"load"]}
        }).cmd("render");
    }

});

/*-- /moduleManager/inx.mod.moduleManager/tableManager/field.js --*/


inx.mod.moduleManager.tableManager.field = inx.dialog.extend({

    constructor:function(p) {
        p.width = 400;
        p.title = "Редактирование поля";
        this.form = inx({
            type:"inx.form",
            style:{
                maxHeight:400,
                border:0,
                vscroll:true
            },            
            bbar:[{
                type:"inx.button",
                text:"Сохранить",
                onclick:[this.id(),"save"]
            }]
        });
        p.items = [this.form];
        
        p.side = [this.textConf];
        
        this.base(p);
        this.call({cmd:"moduleManager:tableManager:getField",tableID:this.tableID,fieldID:this.fieldID},[this.id(),"handleData"]);
        this.on("submit","save");
        inx.hotkey("esc",[this.id(),"destroy"]);
    },
    
    /**
     * Обрабатывает пришедшие данные с настройками поля 
     * Строит форму редактирования поля
     **/
    cmd_handleData:function(p) {
    
        var data = p.field;
        this.form.cmd("add",{
            name:"name",
            label:"Имя (en.)",
            type:"inx.textfield",
            value:data.name,width:150
        });
        
        this.form.cmd("add",{
            name:"type",
            label:"Тип",
            type:"inx.select",
            width:150,
            loader:{cmd:"moduleManager_tableManager:getFieldTypes"},
            value:data.type,
            onchange:[this.id(),"setFieldType"]
        });
                
        this.form.cmd("add",{
            name:"label",
            label:"Метка (рус.)",
            type:"inx.textfield",
            value:data.label,
            width:150
        });
        
        this.form.cmd("add",{
            name:"group",
            label:"Группа (рус.)",
            type:"inx.textfield",
            value:data.group,
            width:150
        });
        
        this.form.cmd("add",{
            name:"default",
            label:"По умолчанию",
            type:"inx.textfield",
            value:data.default,
            width:150
        });
        
        this.form.cmd("add",{name:"editable",label:"Редактируемое",type:"inx.select",value:data.editable,data:[
            {text:"Нет",id:0},
            {text:"Да",id:1},
            {text:"Только чтение",id:2}
        ]});        
        
        
        this.form.cmd("add",{
            name:"indexEnabled",
            label:"Индексировать",
            type:"inx.checkbox",
            value:data.indexEnabled
        });
        
        this.form.cmd("add",{
            name:"help",
            label:"Подсказка",
            type:"inx.textarea",
            value:data.help
        });        
        
        // Дополнительная конфигурация
        this.conf = this.form.cmd("add",p.conf);
        
        // Описание поля
        //this.descr.cmd("setFieldType",data.type);
    },
    
    cmd_setFieldType:function(id) {
        this.call({
            cmd:"moduleManager:tableManager:getFieldConf",
            tableID:this.tableID,
            fieldID:this.fieldID,
            typeID:id
        },[this.id(),"handleFieldDescription"]);
    },    
    
    cmd_handleFieldDescription:function(conf) {
        inx(this.conf).cmd("destroy");
        this.conf = this.form.cmd("add",conf);
    },
    
    /**
     * Отправляет настройки поля на сервер
     **/
    cmd_save:function() {
        var data = this.info("data");
        this.call({cmd:"moduleManager:tableManager:saveField",tableID:this.tableID,fieldID:this.fieldID,data:data},[this.id(),"handleSave"]);
    },
    
    /**
     * Коллбэк сохранения формы
     * Закрывает окно редактирования
     **/
    cmd_handleSave:function() {
        this.fire("save");
        this.task("destroy");
    }

})

/*-- /moduleManager/inx.mod.moduleManager/tableManager/group.js --*/


inx.mod.moduleManager.tableManager.group = inx.dialog.extend({

    constructor:function(p) {
        p.width = 400;
        p.title = "Редактирование группы";
        
        this.form = inx({
            type:"inx.form",
            style:{
                maxHeight:400,
                border:0,
                vscroll:true
            },                        
            bbar:[{
                type:"inx.button",
                text:"Сохранить",
                onclick:[this.id(),"save"]
            }]
        });
        p.items = [this.form];
        
        this.base(p);
        
        this.call({
            cmd:"moduleManager/tableManager/fieldGroup/getFieldGroup",
            tableID:this.tableID,
            groupID:this.groupID
        },[this.id(),"handleData"]);
        
        this.on("submit","save");
        inx.hotkey("esc",this.id(),"destroy");
    },
    
    /**
     * Обрабатывает пришедшие данные с настройками поля 
     * Строит форму редактирования поля
     **/
    cmd_handleData:function(p) {
    
        if(!p)
            return;
    
        var data = p.groupData;
        
        this.form.cmd("add",{
            name:"title",
            label:"Название",
            type:"inx.textfield",
            value:data.title,
            width:150
        });        
       
    },    
    
    /**
     * Отправляет настройки поля на сервер
     **/
    cmd_save:function() {
        var data = this.info("data");
        this.call({
            cmd:"moduleManager/tableManager/saveField",
            tableID:this.tableID,
            fieldID:this.fieldID,
            data:data
        },[this.id(),"handleSave"]);
    },
    
    /**
     * Коллбэк сохранения формы
     * Закрывает окно редактирования
     **/
    cmd_handleSave:function() {
        this.fire("save");
        this.task("destroy");
    }

})

/*-- /moduleManager/inx.mod.moduleManager/tableManager/index.js --*/


inx.mod.moduleManager.tableManager.index = inx.dialog.extend({

    constructor:function(p) {    
        p.width = 400;
        p.title = "Редактирование индекса";
        this.form = inx({
            type:"inx.form",
            border:0,
            maxHeight:400,
            bbar:[{
                type:"inx.button",
                text:"Сохранить",
                onclick:[this.id(),"save"]
            }]
        });
        p.items = [this.form];
        
        this.base(p);
        this.call({
            cmd:"moduleManager:tableManager:getIndex",
            tableID:this.tableID,
            indexID:this.indexID
        },[this.id(),"handleData"]);
        this.on("submit","save");
        inx.hotkey("esc",[this.id(),"destroy"]);
    },
    
    cmd_handleData:function(p) {
    
        if(!p) {
            this.cmd("destroy")
            return;
        }
    
        var data = p.index;
        this.form.cmd("add",{
            name:"name",
            label:"Имя (en.)",
            type:"inx.textfield",
            value:data.name
        });
        
        this.form.cmd("add",{
            name:"fields",
            label:"Поля (через запятую)",
            type:"inx.textarea",
            value:data.fields
        });
        
        this.form.cmd("add",{
            name:"type",
            label:"Тип",
            type:"inx.select",
            value:data.type,
            data:[{
                id:"index",
                text:"Индекс"
            },{
                id:"fulltext",
                text:"Полнотекстовый"
            }]
        });  
    },
        
    cmd_save:function() {
        var data = this.info("data");
        this.call({
            cmd:"moduleManager:tableManager:saveIndex",
            tableID:this.tableID,
            indexID:this.indexID,
            data:data
        },[this.id(),"handleSave"]);
    },
    
    cmd_handleSave:function() {
        this.fire("save");
        this.task("destroy");
    }

})

