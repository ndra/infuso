// @include inx.iframe,inx.date

inx.ns("inx.mod.board.report").gallery = inx.iframe.extend({

    constructor:function(p) {       
    
        p.src = "/board_controller_report/gallery";
        
        this.base(p);        
    }
         
});