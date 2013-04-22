// @link_with_parent
// @include inx.list

inx.mod.board.task.subtasks.add = inx.panel.extend({

    constructor:function(p) {

        p.layout = "inx.layout.column";
        
        p.style = {
            background:"none",
            spacing:2,
            padding:0
        }
        
        p.items = [{
            type:"inx.textfield",
            name:"text",
            width:300
        },{
            type:"inx.textfield",
            width:20,
            name:"timeScheduled"
        },{
            type:"inx.button",
            air:true,
            icon:"plus",
            onclick:[this.id(),"addSubtask"]
        }]
        
        this.base(p);
    },
    
    cmd_addSubtask:function() {
        this.call({
            cmd:"board/controller/task/addEpicSubtask",
            taskID:this.taskID,
            data:this.info("data")
        },[this.id(),"handleAddSubtask"])
    },
    
    cmd_handleAddSubtask:function() {
        this.items().cmd("setValue","");
        this.fire("subtaskAdded");
    }
     
});