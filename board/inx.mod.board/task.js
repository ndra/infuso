// @include inx.dialog,inx.form

inx.ns("inx.mod.board").task = inx.dialog.extend({

    constructor:function(p) {
    
        p.title = "Редактирование задачи";
        p.width = 500;   
        p.modal = true;
        
        p.style = {
            border:0,
            background:"#ededed",
            padding:5
        }
        
        this.base(p);

        this.cmd("requestData");
            
        inx.hotkey("esc",[this.id(),"destroy"]);        
        this.on("submit",[this.id(),"save"]); 
           
        // При закрытии диалога, если были какие-нибудь изменения,
        // обновляем списко задач
        this.on("destroy",function() {
            if(this.taskChanged) {
                this.fire("change");
            }
        });
        
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
    
        this.cmd("setTitle",data.title);
    
        this.data = data;
    
        // Описание задачи
        this.form.cmd("add",{
            type:"inx.textarea",
            value:data.text,
            label:"Описание задачи",
            name:"text",
            style : {
                width:"parent",
                height:"content"
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
            width:120,
            style:{
                border:0,
                background:"none"
            }
        });
        
        buttons.cmd("add",{
            type:"inx.button",
            text:data.nextStatusText,
            onclick:inx.cmd(this.id(),"changeStatus",data.nextStatusID)
        });
        
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
            hidden:true,
            listeners:{
                changeStatus:[this.id(),"changeStatus"]
            }
        });
        
        this.cmd("addSidePanel",{
            type:this.type+".attachments",
            taskID:this.taskID,
            region:"bottom"
        })
        
    },
    
    cmd_toggleMore:function() {
        var side = inx(this.more);
        side.cmd(side.info("visible") ? "hide" : "show");
    },
 
    cmd_handleSave:function(ret) {
        if(ret) {
            this.cmd("registerChanges");
        }
    },
    
    cmd_save:function() {
    
        var data = this.form.info("data");
    
        this.call({
            cmd:"board:controller:task:saveTask",
            data:data,
            taskID:this.taskID,
            status:this.status
        },[this.id(),"handleSave"]);
    },
    
    cmd_changeStatus:function(status) {
    
        var time = 0;
    
        if(this.currentStatus==1) {
            var time = prompt("Сколько было потрачено времени?");
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
        this.cmd("registerChanges");
    },
    
    cmd_registerChanges:function() {
        this.taskChanged = true;
    }
         
});