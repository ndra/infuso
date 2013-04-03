// @include inx.tabs

inx.ns("inx.mod.board").reports = inx.tabs.extend({

    constructor:function(p) {
        p.selectNew = false;
        p.items = [{
            title:"Инфорграфика",
            type:"inx.mod.board.reports.infograph",
            resizable:true,
            lazy:true
        },{
            title:"Бла-бла",
            type:"inx.mod.board.reports.blahblah",
            resizable:true,
            lazy:true
        },{
            title:"Выполненные работы",
            type:"inx.mod.board.reports.done",
            resizable:true,
            lazy:true
        },{
            title:"По проектам",
            type:"inx.mod.board.reports.project",
            resizable:true,
            lazy:true
        }];
        this.base(p); 
    }
         
});