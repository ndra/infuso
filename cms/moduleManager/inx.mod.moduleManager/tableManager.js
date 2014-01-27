// @include inx.list,inx.form,inx.select

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
