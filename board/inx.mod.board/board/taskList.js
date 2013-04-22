// @include inx.list
// @link_with_parent

inx.mod.board.board.taskList = inx.list.extend({

    constructor:function(p) {    

        p.loader = {
            cmd:"board_controller_task::listTasks",
            status:p.status
        };
        
        p.layout = "inx.layout.column";
        
        if(!p.style) {
            p.style = {};
        }
        
        p.style.spacing = 10;
        
        p.sortable = true;
    
        this.base(p);
        
        this.on("itemclick",[this.id(),"handleItemClick"]);
        this.on("data",[this.id(),"handleData"]);
    },
    
    cmd_handleData:function(data,fullData) {
    
        for(var i in data) {
            if(data[i].id=="new") {
                return;
            }
        }
                
        data.unshift({
            id:"new",
            data:{
                text:"Новая задача"
            }            
        });
        
    },
    
    info_itemType:function() {
        return "inx.mod.board.board.taskList.task";
    },
    
    cmd_handleItemClick:function(id) {
        this.cmd("editTask",id);
    },
    
    /**
     * Открывает диалог редактирования задачи
     **/
    cmd_editTask:function(taskID) {
    
        if(taskID=="new") {
            this.cmd("newTask");
            return;
        }
    
        var clip = this.info("itemComponent",taskID).info("param","el");
    
        var task = inx({
            type:"inx.mod.board.task",
            taskID:taskID,
            clipTo:clip
        }).on("change",[this.id(),"load"]);
        
        task.cmd("render");
    },
    
    cmd_newTask:function() {
        this.call({
            cmd:"board/controller/task/newTask"
        },[this.id(),"editTask"]);
    }
         
});