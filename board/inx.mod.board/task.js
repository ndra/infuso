// @include inx.dialog,inx.form,inx.textarea

inx.ns("inx.mod.board").task = inx.dialog.extend({

    constructor:function(p) {
    
        p.clipTo = false;
        p.clipToOwner = false;
    
        p.title = "Редактирование задачи";
        p.showTitle = false;
        p.modal = true;
        
        p.style = {
            padding:20,
            spacing:10,
            width: 1000,
        }
        
        p.destroyOnEscape = true;
        
        this.base(p);

        this.cmd("requestData");
                 
        this.on("submit",[this.id(),"save"]); 
      
        this.extend({
            getMainComponent:function() {
                return inx(this).axis("parents").eq("type","inx.mod.board.main");
            }
        })
        
    },
    
    cmd_requestData:function() {
        this.call({
            cmd:"board/controller/task/getTask",
            taskID:this.taskID
        },[this.id(),"handleData"])
    },
    
    cmd_handleData:function(data) {

        if(!data) {
            this.task("destroy");
            return;
        }
        
        this.items().cmd("destroy");
        
        this.data = data;

        // Описание задачи и время
        this.cmd("add",{
            type:this.info("type")+".project",
            taskID:this.taskID,
            data:data
        });
        
        // Описание задачи и время
        this.cmd("add",{
            type:"inx.textarea",
            name:"text",
            style : {
                width:"parent",
                fontSize:16,
                height:"content"
            }, value:data.text
        }).cmd("focus");

        this.cmd("add",{
            type:"inx.mod.board.taskControls",
            big:true,
            region:"top",
            tools:data.tools,
            taskID:this.taskID,
            listeners:{
                action:[this.id(),"save"]
            }
        });

        
        this.cmd("add",{
            type:"inx.mod.board.task.subtasks",
            taskID:this.taskID
        });
        
        this.cmd("add",{
            type:"inx.separator"
        });

        this.cmd("add",{
            type:"inx.mod.board.attachments",
            dropArea:this,
            name:"attachments",
            taskID:this.taskID,
            region:"bottom",
            onload:function(data) {
                if(data.length) {
                    this.cmd("show");
                } else {
                    this.cmd("hide");
                }
            }
        });
        
        this.cmd("add",{
            type:"inx.separator"
        });
        
        this.cmd("add",{
            type:"inx.mod.board.task.tags",
            name:"tags",
            taskID:this.taskID,
            region:"bottom"
        });
        
        if(!inx(this).axis("side").eq("name","comments").exists()) {
            this.cmd("addSidePanel",{
                type:"inx.mod.board.comments",
                name:"comments",
                resizable:true,
                taskID:this.taskID,
                region:"right",
                width:300
            })
        }
        
    },
    
    cmd_toggleMore:function() {
        var side = inx(this.more);
        side.cmd(side.info("visible") ? "hide" : "show");
    },
    
    cmd_save:function() {
    
        var data = this.form.info("data");
    
        this.call({
            cmd:"board:controller:task:saveTask",
            data:data,
            taskID:this.taskID,
            status:this.status
        });
    },
    
    cmd_handleTimeInput:function() {
        this.cmd("save");
        this.cmd("handleSetStatus");
    },
    
    cmd_changeStatus:function(status) {
    
        var time = 0;
    
        if(this.currentStatus==1) {
            inx({
                type:"inx.mod.board.timeInput",
                taskID:this.taskID,
                taskStatus:status,
                listeners:{
                    save:[this.id(),"handleTimeInput"]
                }
            }).cmd("render");
            return;
        }
    
        this.cmd("save");
    
        this.call({
            cmd:"board/controller/task/changeTaskStatus",
            taskID:this.taskID,
            status:status,
            time:time
        },[this.id(),"handleSetStatus"]);
                
    },
    
    cmd_handleSetStatus:function() {
        this.cmd("requestData");
    }
         
});