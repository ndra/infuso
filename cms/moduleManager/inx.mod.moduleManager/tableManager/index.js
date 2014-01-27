// @link_with_parent
// @include inx.form,inx.dialog

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