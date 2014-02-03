// @include inx.list

inx.ns("inx.mod.board").taskList = inx.list.extend({

    constructor:function(p) {    

        p.loader = {
            cmd:"infuso/board/controller/task/listTasks",
            parentTaskID:p.parentTaskID,
            status:p.status
        };
        
        p.layout = "inx.layout.column";
        
        if(!p.style) {
            p.style = {};
        }
        
        if(p.style.padding===undefined) {
            p.style.padding = 15;
        }
        
        p.style.valign = "top";
        
        p.sortable = true;
        this.on("sortcomplete",[this.id(),"handleSortComplete"]);
        this.on("sortbegin",[this.id(),"handleSortBegin"]);
    
        this.base(p);
        
        this.cmd("setViewMode",p.viewMode);
        
        this.on("load",[this.id(),"handleLoad"]);
        this.on("beforeload",[this.id(),"beforeLoad"]);
        this.on("boardChanged",[this.id(),"load"]);
        
        inx.hotkey("f5",[this.id(),"handleF5"]);
        
        inx.on("board/taskChanged",[this.id(),"handleTaskChanged"]);   
        
        setInterval(inx.cmd(this.id(),"load"),1000*60);     
        
    },
    
    cmd_setViewMode:function(viewMode) {
        switch(viewMode) {
            default:
                this.itemType = "inx.mod.board.taskList.task";
                this.style("spacing",30);
                break;
            case "compact":
                this.itemType = "inx.mod.board.taskList.compact";
                this.style("spacing",0);
                break;
        }
    },
    
    cmd_handleTaskChanged:function(params) {      
    
        if(!this.info("visibleRecursive")) {
            return;
        }
        
        this.cmd("set",params.taskID,params.sticker);
        
        if(params.changed.indexOf("status") != -1) {
            this.cmd("load");
        }
        
    },
    
    cmd_handleLoad:function(data) {
    
        this.sortEnabled = data.sortable;
        
        this.task("load",1000*60*2)
        
    },
    
    cmd_handleF5:function() {
        this.cmd("load");
        return false;
    },
    
    info_itemType:function(data) {
    
        if(data.dateMark) {
            return "inx.mod.board.taskList.dateMark";
        }
        
        return this.itemType;
    },
    
    cmd_handleItemClick:function(id,event) {
    
        if(event.ctrlKey) {
            var projectID = this.info("item",id,"projectID");
            this.getMainComponent().cmd("highlightProject",projectID);
            return;
        }
    
       // this.cmd("editTask",id);
    },
    
    /**
     * Открывает диалог редактирования задачи
     **/
    cmd_editTask:function(taskID,options) {
    
        if(!options) {
            options = {};
        }
    
        if(taskID=="new") {
            return;
        }
        
        if(taskID=="drawback") {
            this.cmd("newDrawback");
            return;
        }
        
        window.location.hash = "task/id/"+taskID;
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