

inx.ns("inx.mod.board").taskControls = inx.panel.extend({

    constructor:function(p) {   
    
        p.style = {
            spacing:0,
            border:0,
            background:"none"
        }
    
        p.layout = "inx.layout.column";
        inx.on("board/taskChanged",[this.id(),"handleTaskChanged"]);
                    
        this.base(p);
        
        this.cmd("handleData",p.tools);
        
    },
    
    cmd_handleData:function(tools) {
    
        this.items().cmd("destroy");
        
        var buttons = {
            add:{
                icon:"add",
                onclick:[this.id(),"addTask"],
                text:"К исполнению"
            }, pause:{
                icon:"pause",
                onclick:[this.id(),"pauseTask"],
                text:"Пауза"
            }, resume: {
                icon:"resume",
                onclick:[this.id(),"pauseTask"],
                text:"Продолжить"
            }, done: {
                icon:"done",
                onclick:[this.id(),"doneTask"],
                text:"Выполнено"
            }, take: {
                icon:"take",
                onclick:[this.id(),"takeTask"],
                text:"Взять"
            }, stop: {
                icon:"stop",
                onclick:[this.id(),"stopTask"],
                text:"Положить обратно"
            }, complete: {
                icon:"complete",
                onclick:[this.id(),"completeTask"],
                text:"Завершить"
            }, revision: {
                icon:"notready",
                onclick:[this.id(),"revisionTask"],
                text:"Не готово"
            }, vote: {
                icon:"vote",
                onclick:[this.id(),"voteTask"],
                text:"Голосовать"
            }    
        }
        
        for(var i in tools) {
        
            var button = buttons[tools[i]];
            
            this.cmd("add",{
                type:"inx.button",
                air:true,
                icon:"/board/res/img/icons"+(this.big ? 64 : 16)+"/"+button.icon+".png",                
                help:button.text,
                text: this.big ? button.text : null,
                height:(this.big ? 80 : 16) + 4*2,
                style:{
                    iconWidth:(this.big ? 64 : 16),
                    iconAlign:(this.big ? "top" : "left"),
                    iconHeight:(this.big ? 64 : 16),
                }, onclick:button.onclick
            });
        }
    
    },
    
    cmd_handleTaskChanged:function(p) {
        if(p.taskID!=this.taskID) {
            return;
        }
        this.cmd("handleData",p.sticker.tools);
    },
    
    cmd_pauseTask:function() {
    
        this.fire("action");
    
        this.call({
            cmd:"board/controller/task/pauseTask",
            taskID:this.taskID
        });
    },
    
    cmd_takeTask:function() {
    
        this.fire("action");
    
        this.call({
            cmd:"board/controller/task/changeTaskStatus",
            taskID:this.taskID,
            status:1
        });
    },
    
    cmd_doneTask:function() {
    
        this.fire("action");
    
        inx({
            type:"inx.mod.board.timeInput",
            taskID:this.taskID,
            taskStatus:2,
            listeners:{
                save:[this.id(),"handleTimeInput"]
            }
        }).cmd("render");
    },
    
    /**
     * Переводит задачу в статус выполнено
     **/
    cmd_completeTask:function() {
    
        this.fire("action");
    
        this.call({
            cmd:"board/controller/task/changeTaskStatus",
            taskID:this.taskID,
            status:3
        });
    },
    
    /**
     * Переводит задачу в статус выполнено
     **/
    cmd_stopTask:function() {
    
        this.fire("action");
    
        this.call({
            cmd:"board/controller/task/changeTaskStatus",
            taskID:this.taskID,
            status:0
        });
    },    
    
    /**
     * Переводит задачу в статус к исполнению
     **/
    cmd_addTask:function() {
    
        this.fire("action");
    
        this.call({
            cmd:"board/controller/task/changeTaskStatus",
            taskID:this.taskID,
            status:0
        });
    },
    
    cmd_revisionTask:function() {    
    
        this.fire("action");
        
        inx({
            type: 'inx.mod.board.returnTask', 
            taskID:this.taskID,
            status:0,
        }).cmd('render');    
    },
    
    cmd_voteTask:function() {
        window.location.href = "#vote/id/"+this.taskID;
    }
         
});