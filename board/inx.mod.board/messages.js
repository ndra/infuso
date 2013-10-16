// @include inx.list

inx.ns("inx.mod.board").messages = inx.list.extend({

    constructor:function(p) {    
    
        p.loader = {
            cmd:"board/controller/messages/list"
        }
        this.base(p); 
        
        this.on("itemclick",[this.id(),"handleItemClick"]);
        inx.hotkey("f5",[this.id(),"handleF5"]);
    },
    
    cmd_handleF5:function() {
        this.cmd("load");
        return false;
    },
    
    cmd_handleItemClick:function(id) {
    }
         
});