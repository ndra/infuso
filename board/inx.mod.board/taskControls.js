

inx.ns("inx.mod.board").taskControls = inx.panel.extend({

    constructor:function(p) {   
    
        if(this.big) {        
            p.style = {
                spacing:5,
                border:0,
                padding:10
            }        
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
                text:"Выполнено",
                style:{
                    color:"white",
                    background:"green"
                }
            }, take: {
                icon:"take",
                onclick:[this.id(),"takeTask"],
                text:"Взять",
                style:{
                    color:"white",
                    background:"green"
                }
            }, stop: {
                icon:"stop",
                onclick:[this.id(),"stopTask"],
                text:"Вернуть"
            }, complete: {
                icon:"complete",
                onclick:[this.id(),"completeTask"],
                text:"Завершить"
            }, revision: {
                icon:"notready",
                onclick:[this.id(),"revisionTask"],
                text:"Не готово"
            }, cancel: {
                icon:"close",
                onclick:[this.id(),"cancelTask"],
                text:"Отменить"
            }    
        }
        
        for(var i in tools) {
        
            
            
            if(tools[i]=="|") {
            
                this.cmd("add",{
                    width:this.big ? 20 : 5
                })
                
            
            } else {
            
                var button = buttons[tools[i]];
                
                var style = {
                    iconWidth:(this.big ? 16 : 16),
                    iconAlign:"left",
                    iconHeight:(this.big ? 24 : 16),
                    fontSize:18,
                    padding: this.big ? 10 : 0,
                    shadow:this.big ? true : false,
                    height:(this.big ? 24 : 16) + 4*2,
                }
                
                if(button.style) {
                    for(var j in button.style) {
                        style[j] = button.style[j];
                    }
                }
            
                this.cmd("add",{
                    type:"inx.button",
                    air:true,
                    icon:"/board/res/img/icons16/"+button.icon+".png", 
                    help:button.text,
                    text: this.big ? button.text : null,                    
                    style:style,
                    onclick:button.onclick
                });
            }
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