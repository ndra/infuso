

inx.ns("inx.mod.board").taskControls = inx.panel.extend({

    constructor:function(p) {   
    
        if(p.big) {        
            p.style = {
                spacing:5,
                border:0
            }        
        } else {
            p.style = {}
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
                general: {
                    icon:"/board/res/img/icons16/add.png",
                    onclick:[this.id(),"addTask"],
                    help:"К исполнению",
                }, large: {
                    icon:"/board/res/img/icons24/add.png",
                    text:"Добавить"
                }
            }, pause:{
                general: {
                    help:"Пауза",
                }, small: {
                    icon:"/board/res/img/icons16/pause.png",
                }, large: {
                    text:"Пауза",
                    type:this.info("type")+".time"
                }
            }, resume: {
                general: {
                    help:"Продолжить",
                    onclick:[this.id(),"pauseTask"],
                }, small: {
                    air:true,
                    icon:"/board/res/img/icons16/resume.png",
                }, large :{
                    icon:"/board/res/img/icons24/resume.png",
                }
                
            }, done: {
                general: {
                    icon:"/board/res/img/icons16/done.png",
                    onclick:[this.id(),"doneTask"]
                }, small:{
                    air:true
                }, large:{
                    text:"Выполнено",
                    icon:"/board/res/img/icons24/done.png",
                    style:{
                        color:"white",
                        background:"green"
                    }
                }             
            }, problems: {
                general: {
                    icon:"/board/res/img/icons24/problems.png"
                }, small: {
                    icon:"/board/res/img/icons16/problems.png",
                    air:true
                }
            }, take: {
                general: {
                    onclick:[this.id(),"takeTask"],
                    help:"Взять"
                }, small: {
                    icon:"/board/res/img/icons16/take.png",
                }, large: {
                    icon:"/board/res/img/icons24/take.png",
                    text:"Взять"
                }
            }, stop: {
                general: {
                    icon:"/board/res/img/icons24/stop.png",
                    onclick:[this.id(),"stopTask"],
                    help:"Вернуть"
                }, small: {
                    icon:"/board/res/img/icons16/stop.png",
                    air:true
                }
            }, complete: {
                general: {
                    icon:"complete",
                    onclick:[this.id(),"completeTask"],
                }, large: {
                    icon:"/board/res/img/icons24/complete.png",
                    text:"Завершить",
                    style:{
                        color:"white",
                        background:"green"
                    }
                }
            }, revision: {
                general: {
                    icon:"notready",
                    onclick:[this.id(),"revisionTask"],
                    help:"Не готово"                    
                }, large: {
                    icon:"/board/res/img/icons24/revision.png",
                    text:"Не готово"
                }, small: {
                    icon:"/board/res/img/icons16/revision.png",
                    air:true
                }
            }, cancel: {
                general: {                    
                    onclick:[this.id(),"cancelTask"],
                    help:"Отменить"
                }, large: {
                    icon:"/board/res/img/icons24/cancel.png",
                }, small: {
                    icon:"/board/res/img/icons16/cancel.png",
                    air:true
                }
            }    
        }
        
        if(!this.tools.main) {
            return;
        }
        
        var tools = [];
        
        if(this.showMain) {
            tools = tools.concat(this.tools.main);
        }
        
        if(this.showAdditional) {        
            tools = tools.concat(this.tools.additional);
        }
        
        for(var i in tools) {
            
            if(tools[i]=="|") {
            
                this.cmd("add",{
                    width:this.big ? 20 : 10
                });
            
            } else {
            
                var button = inx.deepCopy(buttons[tools[i]]).general;
                                
                if(!button.type) {
                    button.type = "inx.button";
                }
                
                var style = this.big ? {
                    iconWidth: 24,
                    iconHeight: 24,
                    fontSize: 18,
                    padding: 10,
                    height: 28 + 4*2,
                } : {
                    
                };
                
                // Добавляем стили по умолчанию
                if(!button.style) {
                    button.style = {};
                }
                for(var j in style) {
                    button.style[j] = style[j];
                }
                
                // Добавляем стили для большой / маленькой кнопки                
                var extra = inx.deepCopy(buttons[tools[i]])[this.big ? "large" : "small"];
                if(extra!==false) {
                    button = $.extend(true,button,extra);
                    this.cmd("add",button);
                }
            }
        }
    
    },
    
    cmd_handleTaskChanged:function(p) {
        if(p.taskID!=this.taskID) {
            return;
        }
        this.cmd("handleData",p.sticker.tools);
    },
    
    cmd_execCommand:function(cmd) {
        switch(cmd) {
            case "pause":
                this.cmd("pauseTask");
                break;
            case "resume":
                this.cmd("pauseTask");
                break;
            case "done":
                this.cmd("doneTask");
                break;
            case "take":
                this.cmd("takeTask");
                break;
            case "add":
                this.cmd("addSubtask");
                break;
            case "edit":
                this.cmd("editTask");
                break;
        }
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
    
        inx({
            type:"inx.mod.board.timeInput",
            taskID:this.taskID,
            taskStatus:0,
            listeners:{
                save:[this.id(),"handleTimeInput"]
            }
        }).cmd("render");
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
    
    /**
     * Переводит задачу в статус к исполнению
     **/
    cmd_cancelTask:function() {
    
        if(!window.confirm("Отменить задачу?")) {
            return;
        }
    
        this.call({
            cmd:"board/controller/task/changeTaskStatus",
            taskID:this.taskID,
            status:100
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
    
    cmd_editTask:function() {    
        window.location.href = "#task/id/"+this.taskID;
    },
    
    cmd_addSubtask:function() {
        inx({
            taskID:this.taskID,
            type:"inx.mod.board.addSubtask",
            clipToOwner:true
        }).cmd("render").setOwner(this.owner());
    }
         
});