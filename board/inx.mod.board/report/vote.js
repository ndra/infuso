// @include inx.iframe

inx.ns("inx.mod.board.report").vote = inx.iframe.extend({

    constructor:function(p) {
        p.src = "/board_controller_report/vote";
        this.base(p); 
        inx.hotkey("f5",[this.id(),"handleF5"]);
    },
    
    cmd_handleF5:function() {
        this.cmd("refresh");
        return false;
    }
         
});