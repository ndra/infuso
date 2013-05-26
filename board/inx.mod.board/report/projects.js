// @include inx.iframe

inx.ns("inx.mod.board.report").projects = inx.iframe.extend({

    constructor:function(p) {       
        p.src = "/board_controller_report/projects";
        this.on("show","refresh");
        this.base(p);        
    }
         
});