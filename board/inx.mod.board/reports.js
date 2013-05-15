// @include inx.tabs

inx.ns("inx.mod.board").reports = inx.tabs.extend({

    constructor:function(p) {
        p.selectNew = false;
        p.items = [{
            title:"Пользователи",
            type:"inx.mod.board.reports.infograph",
            resizable:true,
            lazy:true
        },{
            title:"Проекты",
            type:"inx.mod.board.reports.project",
            resizable:true,
            lazy:true
        }];
        this.base(p); 
    }
         
});