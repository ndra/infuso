// @link_with_parent
// @include inx.iframe

inx.mod.board.main.informer = inx.iframe.extend({

    constructor:function(p) {

        p.src = "/board_controller_informer";
        inx.hotkey("f5",[this.id(),"handleF5"]);

        this.base(p);
    },
    
    cmd_handleF5:function() {
        this.cmd("refresh");
        return false;
    }
         
});