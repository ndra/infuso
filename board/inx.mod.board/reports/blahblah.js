// @include inx.list

inx.mod.board.reports.blahblah = inx.list.extend({

    constructor:function(p) {
        p.layout = "inx.layout.fit";
        p.loader = {cmd:"board:controller:reportLog"}
        /*p.side = [{
            type:"inx.calendar",
            region:"bottom",
            height:260
        }] */
        this.base(p); 
        inx.hotkey("f5",this.id(),"handleF5");
    },
    
    cmd_handleF5:function() {
        this.task("load");
        return false;
    }
         
});