// @link_with_parent
// @include inx.dialog

inx.mod.board.task.subtasks.add.dlg = inx.dialog.extend({

    constructor:function(p) {

        p.layout = "inx.layout.column";
        
        p.modal = false;
        
        p.showTitle = false;
        
        p.destroyOnEscape = true;
        
        p.style = {
            width:380,
            border:0,
            background:"none"
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
        },{
            type:"inx.button",
            air:true,
            icon:"delete",
            onclick:[this.id(),"destroy"]
        }];
        
        this.base(p);
        this.on("submit",[this.id(),"addSubtask"]);
        this.on("render",function() {
            inx(this).items().eq("name","text").task("focus");
        });
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
        this.cmd("destroy");
    }
     
});