// @include inx.panel

inx.ns("inx.mod.board").board = inx.panel.extend({

    constructor:function(p) {
    
        p.layout = "inx.layout.fit";
    
        this.taskList = inx({
            type:"inx.mod.board.board.taskList",
            status:p.status,
            style:{
                border:0,
                height:"parent"
            }
        });
        
        p.items = [this.taskList];
        
        this.taskList.on("beforeload",[this.id(),"handleBeforeLoad"]);
        this.taskList.on("load",[this.id(),"handleLoad"]);
    
        this.base(p);
    },
    
    cmd_handleBeforeLoad:function(data) {
        this.fire("beforeload",data);
    },
    
    cmd_handleLoad:function(data) {
        this.fire("load",data);
    },
    
    cmd_load:function() {
        this.taskList.cmd("load");
    }

         
});