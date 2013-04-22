// @include inx.panel

inx.ns("inx.mod.board").board = inx.panel.extend({

    constructor:function(p) {
    
        p.layout = "inx.layout.fit";
    
        this.taskList = inx({
            type:"inx.mod.board.board.taskList",
            status:p.status,
            style:{
                border:0,
                height:400
            }
        })
        
        p.items = [this.taskList];
    
        this.base(p);
    }

         
});