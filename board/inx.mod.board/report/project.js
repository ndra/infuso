// @include inx.iframe

inx.ns("inx.mod.board.report").project = inx.iframe.extend({

    constructor:function(p) {       
        p.src = "/board_controller_report/projectDetailed/projectID/"+p.projectID;
        this.on("show","refresh");
        this.base(p);        
    }
         
});