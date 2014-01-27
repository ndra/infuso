// @link_with_parent
// @include inx.dialog

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