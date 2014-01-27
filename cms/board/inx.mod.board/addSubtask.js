// @include inx.dialog

inx.ns("inx.mod.board").addSubtask = inx.dialog.extend({

    constructor:function(p) {

        p.layout = "inx.layout.column";
        
        p.modal = false;
        
        p.showTitle = false;
        
        p.destroyOnEscape = true;
        
        p.style = {
            width:460,
            border:1,
            padding:10,
            spacing:2,
            background:"#ededed"
        }
        
        p.items = [{
            type:"inx.textfield",
            name:"text",
            style:{
                height:32,
                width:300
            }
        },{
            type:"inx.textfield",
            style:{
                width:40,
                height:32
            },
            name:"timeScheduled"
        },{
            type:"inx.button",
            air:true,
            icon:"/board/res/img/icons24/take.png",
            onclick:[this.id(),"doSubtask"]
        },{
            type:"inx.button",
            air:true,
            icon:"delete",
            onclick:[this.id(),"destroy"]
        }];
        
        this.base(p);
        this.on("submit",[this.id(),"doSubtask"]);
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
    
    cmd_doSubtask:function() {
        this.call({
            cmd:"board/controller/task/addEpicSubtask",
            taskID:this.taskID,
            take:true,
            data:this.info("data")
        },[this.id(),"handleAddSubtask"])
    },
    
    cmd_handleAddSubtask:function() {
        this.items().cmd("setValue","");
        this.fire("subtaskAdded");
        this.cmd("destroy");
    }
     
});