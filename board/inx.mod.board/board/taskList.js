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
        
        p.style.spacing = 15;
        p.style.padding = 15;
        p.style.valign = "top";
        
        p.sortable = true;
        this.on("sortcomplete",[this.id(),"handleSortComplete"]);
        this.on("sortbegin",[this.id(),"handleSortBegin"]);
    
        this.base(p);
        
        this.on("itemclick",[this.id(),"handleItemClick"]);
        this.on("load",[this.id(),"handleLoad"]);
        this.on("boardChanged",[this.id(),"load"]);
        
        inx.hotkey("f5",[this.id(),"handleF5"]);
        
        this.extend({
            getMainComponent:function() {
                return inx(this).axis("parents").eq("type","inx.mod.board.main");
            }
        })
        
        var cmp = this;
        inx.on("board/taskChanged",function(params) {            
            cmp.cmd("set",params.taskID,params.sticker);
        });
        
        
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
    
    cmd_handleF5:function() {
        this.cmd("load");
        return false;
    },
    
    info_itemType:function() {
        return "inx.mod.board.board.taskList.task";
    },
    
    cmd_handleItemClick:function(id,event) {
    
        if(event.ctrlKey) {
            var projectID = this.info("item",id,"projectID");
            this.getMainComponent().cmd("highlightProject",projectID);
            return;
        }
    
        this.cmd("editTask",id);
    },
    
    /**
     * Открывает диалог редактирования задачи
     **/
    cmd_editTask:function(taskID,options) {
    
        if(!options) {
            options = {};
        }
    
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
            showMore: options.showMore,
            clipTo:clip
        }).on("change",function(){
            this.bubble("boardChanged");
        }).setOwner(this);
        
        task.cmd("render");
    },
    
    cmd_newTask:function() {
        this.call({
            cmd:"board/controller/task/newTask"
        },[this.id(),"handleCreateNewTask"]);
    },
    
    cmd_handleCreateNewTask:function(id) {
        this.cmd("editTask",id,{showMore:true});
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