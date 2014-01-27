// @link_with_parent
// @include inx.form,inx.dialog

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