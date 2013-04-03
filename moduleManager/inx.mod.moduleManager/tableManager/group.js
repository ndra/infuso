// @link_with_parent
// @include inx.form,inx.dialog

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