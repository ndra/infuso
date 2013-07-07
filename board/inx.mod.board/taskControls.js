

inx.ns("inx.mod.board").taskControls = inx.panel.extend({

    constructor:function(p) {   
    
        p.style = {
            spacing:4,
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
    
        // Пауза
        if(tools.indexOf("pause")!=-1) {
            this.cmd("add",{
                type:"inx.button",
                air:true,
                icon:"/board/res/img/icons16/pause.png",
                onclick:[this.id(),"pauseTask"]
            });
        }
        
        if(tools.indexOf("resume")!=-1) {
            this.cmd("add",{
                type:"inx.button",
                air:true,
                icon:"/board/res/img/icons16/resume.png",
                onclick:[this.id(),"pauseTask"]
            });
        }
        
        // Я все сделал
        if(tools.indexOf("done")!=-1) {
            this.cmd("add",{
                type:"inx.button",
                help:"Готово",
                air:true,
                icon:"ok",
                onclick:[this.id(),"doneTask"]
            });
        }
        
        // Буду делать
        if(tools.indexOf("take")!=-1) {
            this.cmd("add",{
                type:"inx.button",
                air:true,
                icon:"/board/res/img/icons16/runner.png",
                onclick:[this.id(),"takeTask"]
            });
        }
        
        // Проверено
        if(tools.indexOf("complete")!=-1) {
            this.cmd("add",{
                type:"inx.button",
                help:"Проверено",
                air:true,
                icon:"ok",
                onclick:[this.id(),"completeTask"]
            });
        }
        
        
        // На ревизию
        if(tools.indexOf("revision")!=-1) {
            this.cmd("add",{
                type:"inx.button",
                air:true,
                icon:"delete",
                onclick:[this.id(),"revisionTask"]
            });
        }
        
        // Голосовать
        if(tools.indexOf("vote")!=-1) {
            this.cmd("add",{
                type:"inx.button",
                air:true,
                icon:"hand",
                help:"Голосовать",
                onclick:[this.id(),"voteTask"]
            });
        }
    
    },
    
    cmd_handleTaskChanged:function(p) {
        this.cmd("handleData",p.sticker.tools);
    },
    
    cmd_pauseTask:function() {
        this.call({
            cmd:"board/controller/task/pauseTask",
            taskID:this.taskID
        });
    },
    
    cmd_takeTask:function() {
        this.call({
            cmd:"board/controller/task/changeTaskStatus",
            taskID:this.taskID,
            status:1
        });
    },
    
    cmd_doneTask:function() {
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
        this.call({
            cmd:"board/controller/task/changeTaskStatus",
            taskID:this.taskID,
            status:3
        },[this.id(),"handleSave"])
    },
    
    cmd_revisionTask:function() {        
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