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
        
        if(p.taskID=="new") {
            this.cmd("handleData",{text:"",project:p.projectID,status:p.status});
        } else {
            this.cmd("requestData");
        }
            
        inx.hotkey("esc",[this.id(),"destroy"]);        
        this.on("submit",[this.id(),"save"]);    
        this.on("smoothBlur",[this.id(),"destroy"]);    
    },
    
    cmd_requestData:function() {
        this.call({
            cmd:"board:controller:task:getTask",
            taskID:this.taskID
        },[this.id(),"handleData"])
    },
    
    cmd_handleData:function(data) {
    
        this.cmd("setTitle",data.title);
    
        this.data = data;
    
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
        
        var cmp = this;
        this.form.cmd("add",{
            type:"inx.checkbox",
            label:"Эпик",
            labelWidth:0,
            onchange:function() {
                if(this.info("value")) {
                    cmp.cmd("showSubtasks");
                } else {
                    cmp.cmd("hideSubtasks");
                }
            }
        });
        
        this.subtasks = this.form.cmd("add",{
            type:"inx.mod.board.task.subtasks"
        });
        
        this.form.cmd("add",{
            type:"inx.button",
            icon:"ok",
            text:"Сохранить",
            onclick:[this.id(),"save"]
        }); 
        
    },
    
    /**
     * Показывает список подзадач
     **/
    cmd_showSubtasks:function() {
        this.subtasks.cmd("show");
    },
    
    /**
     * Скрывает список подзадач
     **/
    cmd_hideSubtasks:function() {
        this.subtasks.cmd("hide");
    },
    
    cmd_changeStatus:function(status) {
    
        if(this.data.status==1) {
            var t = window.prompt("Сколько было потрачено времени (в часах)?");
            t = parseFloat(t);
            if(!t) {
                inx.msg("Вы не можете перевести задание в категорию «Выполнено», не указав потраченное время.",1);
                return;
            }
        }
    
        this.call({
            cmd:"board:controller:changeTaskStatus",
            taskID:this.taskID,
            status:status,
            time:t
        },[this.id(),"handleSave"]);
    },
    
    cmd_handleSave:function(ret) {
        if(ret) {
            this.task("destroy");
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