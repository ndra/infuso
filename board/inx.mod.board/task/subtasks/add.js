// @link_with_parent
// @include inx.list

inx.mod.board.task.subtasks.add = inx.panel.extend({

    constructor:function(p) {
        
        p.style = {
            background:"none",
            spacing:2,
            padding:0
        }
        
        p.items = [{
            html:"<a href='#' onclick='return false;' style='display:block;' >Добавить подзадачу</a>",
            width:200,
            listeners:{
                click:[this.id(),"openDialog"]
            },
            style:{
                background:0,
                border:0
            }
        }]
        
        this.base(p);

    },
    
    cmd_openDialog:function(e) {
    
        var cmp = this;
    
        inx({
            type:"inx.mod.board.task.subtasks.add.dlg",
            clipTo:e.target,
            taskID:this.taskID,
            listeners:{
                subtaskAdded:function() {
                    cmp.fire("subtaskAdded");
                }
            }
        }).cmd("render")
    }
     
});