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
            html:"<a href='#' onclick='return false;' style='display:block;' >Добавить подзадачу</a>",
            width:200,
            listeners:{
                click:[this.id(),"more"]
            },
            style:{
                background:0,
                border:0
            }
        },{
            type:"inx.textfield",
            name:"text",
            width:300,
            hidden:true
        },{
            type:"inx.textfield",
            width:20,
            name:"timeScheduled",
            hidden:true
        },{
            type:"inx.button",
            air:true,
            icon:"plus",
            onclick:[this.id(),"addSubtask"],
            hidden:true
        },{
            type:"inx.button",
            air:true,
            icon:"delete",
            onclick:[this.id(),"less"],
            hidden:true
        }]
        
        this.base(p);
        this.on("submit",[this.id(),"addSubtask"]);
    },
    
    cmd_more:function() {
        this.items().get(0).cmd("hide");
        this.items().get(1).cmd("show").task("focus");
        this.items().get(2).cmd("show");
        this.items().get(3).cmd("show");
        this.items().get(4).cmd("show");
    },
    
    cmd_less:function() {
        this.items().get(0).cmd("show");
        this.items().get(1).cmd("hide");
        this.items().get(2).cmd("hide");
        this.items().get(3).cmd("hide");
        this.items().get(4).cmd("hide");
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
        this.cmd("less");
    }
     
});