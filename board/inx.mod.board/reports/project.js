// @include inx.list

inx.mod.board.reports.project = inx.iframe.extend({

    constructor:function(p) {       
        p.src = "/board_controller_report/projects";
        this.on("show","refresh");
        this.base(p);        
    }
         
});