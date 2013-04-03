// @link_with_parent
// @include inx.tabs

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