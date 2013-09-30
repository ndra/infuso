// @link_with_parent
// @include inx.list,inx.mod.board.taskList

inx.mod.board.task.subtasks = inx.mod.board.taskList.extend({

    constructor:function(p) {
    
        p.status = 0;
        
        p.style = {
            border:0
        }
        
        p.viewMode = "compact";

        p.side = [{
            type:p.type+".toolbar",
            taskID:p.parentTaskID,
            region:"top",
            listeners:{
                subtaskAdded:[this.id(),"handleChanges"]
            }
        }]
        
        this.base(p);

   }
     
});