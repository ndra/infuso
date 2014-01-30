// @link_with_parent
// @include inx.list

inx.mod.board.task.subtasks.toolbar = inx.panel.extend({

    constructor:function(p) {
    
        p.layout = "inx.layout.column";
        
        p.style = {
            background:"none",
            spacing:2,
            padding:0
        }
        
        p.items = [{
            type:"inx.button",
            text:"Взять подзадачу",
            icon:"/board/res/img/icons16/add.png",
            air:true,
            listeners:{
                click:[this.id(),"openDialog"]
            },
            style:{
                background:0,
                border:0
            }
        }/*, {
            width:20
        },{
            type:"inx.panel",
            html:"<span style='padding:4px;background:#ccc;display:inline-block;border-radius:5px;' >Активные (5)</span> Выполненные (124)",
            width:220
        }, {
            type:"inx.pager",
            total:5
        } */]
        
        this.base(p);

    },
    
    cmd_openDialog:function(e) {
    
        var cmp = this;
    
        inx({
            type:"inx.mod.board.addSubtask",
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