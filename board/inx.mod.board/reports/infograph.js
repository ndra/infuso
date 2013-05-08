// @include inx.iframe
// @link_with_parent

inx.mod.board.reports.infograph = inx.iframe.extend({

    constructor:function(p) {
        p.src = "/board_controller_report/workers";
        this.base(p); 
        inx.hotkey("f5",[this.id(),"handleF5"]);
    },
    
    cmd_handleF5:function() {
        this.cmd("refresh");
        return false;
    }
         
});