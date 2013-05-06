// @link_with_parent
// @include inx.list

inx.mod.board.task.attachments = inx.list.extend({

    constructor:function(p) {
    
        p.loader = {
            cmd:"board/controller/attachment/listFiles",
            taskID:p.taskID
        }
    
        this.base(p);
        
        this.on("itemdblclick",[this.id(),"handleDblClick"]);
        
    },
    
    cmd_handleDblClick:function(id) {
        var url = this.info("item",id,"url");
        window.open(url);
    }
     
});