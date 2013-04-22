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
        
        this.form = inx({
            type:"inx.form",
            style: {
                border:0,
                background:"none",
                padding:0
            },
            labelWidth:120
        });
        p.items = [this.form];
        
        p.side = [{
            type:"inx.panel",
            width:100,
            region:"right",
            items:[{
                html:"Тэги"
            },{
                html:"Время"
            }]
        }];
        
        this.base(p);

        this.cmd("requestData");
            
        inx.hotkey("esc",[this.id(),"destroy"]);        
        this.on("submit",[this.id(),"save"]);    
        this.on("smoothBlur",[this.id(),"destroy"]);    
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
            name:"subtasks",
            taskID:this.taskID
        });
        
        this.form.cmd("add",{
            type:"inx.button",
            icon:"ok",
            text:"Сохранить",
            onclick:[this.id(),"save"]
        }); 
        
    },
 
    cmd_handleSave:function(ret) {
        if(ret) {
            //this.task("destroy");
            this.fire("change");
        }
    },
    
    cmd_save:function() {
        this.call({
            cmd:"board:controller:task:saveTask",
            data:this.form.info("data"),
            taskID:this.taskID,
            status:this.status
        },[this.id(),"handleSave"]);
    }
         
});