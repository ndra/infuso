// @include inx.form

inx.ns("inx.mod.eshop").attributes = inx.form.extend({

    constructor:function(p) {
        p.tbar = [
            {text:"Сохранить",icon:"save",onclick:[this.id(),"save"]},"|",
            {text:"Удалить атрибуты",icon:"delete",onclick:[this.id(),"deleteAttributes"]}
        ];
        this.base(p);
        inx.hotkey("ctrl+s",this,"save");
        this.task("requestData");
    },
    
    cmd_requestData:function() {
        this.call({
            cmd:"eshop:attr:controller:getAttributes",
            itemID:this.itemID
        },[this.id(),"handleData"]);
    },
    
    cmd_deleteAttributes:function() {
        if(!confirm("Все атрибуты товара будут удалены. Продолжить?"))
            return;
        this.call({
            cmd:"eshop:attr:controller:deleteAttributes",
            itemID:this.itemID
        },[this.id(),"requestData"]);
    },
    
    cmd_handleData:function(data) {
        this.cmd("destroyChildren");
        if(data=="none") {
            this.cmd("add",{
                type:"inx.panel",
                html:"У этого товара пока нет атрибутов.",
                border:0                
            });
            this.cmd("add",{
                type:"inx.button",
                text:"Создать",
                onclick:[this.id(),"createAttributes"]
            })
        } else {
            for(var i in data)
                this.cmd("add",data[i]);
            this.cmd("add",{type:"inx.button",text:"Сохранить",onclick:[this.id(),"save"]});
        }
    },
    
    cmd_createAttributes:function() {
        this.call({
            cmd:"eshop:attr:controller:createAttributes",
            itemID:this.itemID
        },[this.id(),"requestData"]);
    },
    
    cmd_save:function() {
        this.call({cmd:"eshop:attr:controller:saveAttributes",data:this.info("data"),itemID:this.itemID});
        return false;
    }
     
});