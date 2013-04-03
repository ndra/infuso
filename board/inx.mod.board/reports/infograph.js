// @include inx.panel
// @link_with_parent

inx.mod.board.reports.infograph = inx.panel.extend({

    constructor:function(p) {
        this.base(p); 
        inx.hotkey("f5",this.id(),"handleF5");
        this.task("load");
    },
    
    cmd_load:function() {
        this.call({
            cmd:"board:controller:reportInfograph"
        },[this.id(),"html"])
    },
    
    cmd_handleF5:function() {
        this.task("load");
        return false;
    }
         
});