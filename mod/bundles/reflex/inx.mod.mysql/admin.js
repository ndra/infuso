// @include inx.viewport,inx.tabs

inx.ns("inx.mod.mysql").admin = inx.viewport.extend({

    constructor:function(p) {
        var items = [
            {type:"inx.mod.mysql.admin.tables",title:"Таблицы",lazy:true},
            {type:"inx.mod.mysql.admin.query",title:"Запрос",lazy:true}
        ];
        this.tabs = inx({
            type:"inx.tabs",
            items:items
        });
        p.items = [this.tabs];
        this.base(p);
    }

});