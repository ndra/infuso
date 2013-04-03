// @link_with_parent
// @include inx.list

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