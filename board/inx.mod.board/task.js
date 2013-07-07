// @include inx.dialog,inx.form,inx.textarea

inx.ns("inx.mod.board").task = inx.dialog.extend({

    constructor:function(p) {
    
        p.clipTo = false;
        p.clipToOwner = false;
    
        p.title = "Редактирование задачи";
        p.modal = true;
        
        p.style = {
            border:0,
            background:"#ededed",
            padding:5,
            width: $(window).width() - 50,
            height: $(window).height() - 70
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
        
        this.currentStatus = data.currentStatus;
        
        this.items().cmd("destroy");
        
        this.form = inx({
            type:"inx.form",
            style: {
                border:0,
                background:"none",
                padding:0
            },
            labelWidth:120
        });
        this.cmd("add",this.form);
    
        this.cmd("setTitle",data.title+" <a href='#vote/id/"+this.taskID+"' >Оценить</a>");
    
        this.data = data;
    
        // Описание задачи и время
        this.form.cmd("add",{
            type:"inx.panel",            
            label:"Описание задачи",
            items:[{
                type:"inx.textarea",
                name:"text",
                style : {
                    width:"parent",
                    height:"content"
                }, value:data.text
            }], side:[{
                type:"inx.panel",
                width:25,
                region:"right",
                style:{background:"none"},
                items:[{
                    type:"inx.textfield",
                    name:"timeScheduled",
                    value:data.timeScheduled,
                    width:"parent"
                }]
            },{
                width:5,
                region:"right",
                style:{background:"none"}
            }], style : {
                width:"parent",
                background:"none",
                border:0
            }
        }).cmd("focus");
       

        this.form.cmd("add",{
            type:"inx.mod.board.task.subtasks",
            taskID:this.taskID
        });
        
        var buttons = this.form.cmd("add",{
            type:"inx.panel",
            layout:"inx.layout.column",
            style:{
                border:0,
                spacing:4,
                background:"none"
            }
        });
        
        buttons.cmd("add",{
            type:"inx.button",
            icon:"save",
            text:"Сохранить",
            onclick:[this.id(),"save"]
        });
        
        buttons.cmd("add",{
            type:"inx.mod.board.taskControls",
            width:150,
            tools:data.tools,
            taskID:this.taskID,
        });
        
        buttons.cmd("add",{            
            width:120,
            style:{
                border:0,
                background:"none"
            }
        });
        
       /* if(data.nextStatusID!==null) {
            buttons.cmd("add",{
                type:"inx.button",
                text:data.nextStatusText,
                onclick:inx.cmd(this.id(),"changeStatus",data.nextStatusID)
            });
        } */
        
        buttons.cmd("add",{
            type:"inx.button",
            icon:"gear",
            air:true,
            onclick:[this.id(),"toggleMore"]
        });
        
        this.more = this.form.cmd("add",{
            type:this.type+".more",
            region:"bottom",
            data:data,
            hidden:!this.showMore,
            listeners:{
                changeStatus:[this.id(),"changeStatus"]
            }
        });
        
        if(!inx(this).axis("side").eq("name","attachments").exists()) {
            this.cmd("addSidePanel",{
                type:"inx.mod.board.attachments",
                dropArea:this,
                name:"attachments",
                taskID:this.taskID,
                region:"bottom"
            })
        }
        
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