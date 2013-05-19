// @link_with_parent

inx.mod.board.board.taskList.task.controls = inx.panel.extend({

    constructor:function(p) {   
    
        p.style = {
            spacing:4,
            border:0
        }
    
        p.layout = "inx.layout.column";
        
        // Добавляем иконки действий
        p.items = [];
        
        // Пауза
        if(p.tools.indexOf("pause")!=-1) {
            p.items.push({
                type:"inx.button",
                air:true,
                icon:"/board/res/img/icons16/pause.png",
                onclick:[this.id(),"pauseTask"]
            });
        }
        
        // Я все сделал
        if(p.tools.indexOf("done")!=-1) {
            p.items.push({
                type:"inx.button",
                air:true,
                icon:"ok",
                onclick:[this.id(),"completeTask"]
            });
        }
            
        this.base(p);
    },
    
    cmd_pauseTask:function() {
        this.call({
            cmd:"board/controller/task/pauseTask",
            taskID:this.taskID
        });
    },
    
    cmd_completeTask:function() {
        inx({
            type:"inx.mod.board.timeInput",
            taskID:this.taskID,
            taskStatus:2,
            listeners:{
                save:[this.id(),"handleTimeInput"]
            }
        }).cmd("render");
    }
         
});