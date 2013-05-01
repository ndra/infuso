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
        p.style.valign = "top";
        
        p.sortable = true;
        this.on("sortcomplete",[this.id(),"handleSortComplete"]);
        this.on("sortbegin",[this.id(),"handleSortBegin"]);
    
        this.base(p);
        
        this.on("itemclick",[this.id(),"handleItemClick"]);
        this.on("load",[this.id(),"handleLoad"])
        
    },
    
    cmd_handleLoad:function(data) {
    
        this.sortEnabled = data.sortable;
        
        if (data.showCreateButton) {
                
            data.data.unshift({
                id:"drawback"
            });
            
            data.data.unshift({
                id:"new"     
            });

        }
        
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
        
        if(taskID=="drawback") {
            this.cmd("newDrawback");
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
    },
    
    /**
     * Создает новую помеху
     **/
    cmd_newDrawback:function() {
        this.call({
            cmd:"board/controller/task/newDrawback"
        },[this.id(),"editTask"]);
    },
    
    cmd_handleSortBegin:function(itemID) {
    
        if(!this.sortEnabled) {
            return false;
        }
    
        if(itemID==="new") {
            return false;
        }
    },
    
    cmd_handleSortComplete:function() {
    
        var idList = [];
        this.items().each(function() {
            idList.push(this.data("itemID"));
        });
    
        this.call({
            cmd:"board/controller/task/saveSort",
            idList:idList
        });
    }
         
});