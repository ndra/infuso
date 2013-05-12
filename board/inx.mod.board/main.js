// @include inx.viewport,inx.tabs,inx.direct

inx.ns("inx.mod.board").main = inx.viewport.extend({

    constructor:function(p) {
    
        layout = "inx.layout.fit";
    
        this.tabs = inx({
            type:"inx.tabs",
            headComponent:"inx.mod.board.main.headComponent",
            selectNew:false,
            style:{
                height:"parent"
            }
        });        
        
        p.items = [this.tabs];
        
        this.informer = inx({
            type:"inx.mod.board.main.informer",
            region:"right",
            width:300,
            resizable:true
        });
        
        p.side = [this.informer];
        
        this.base(p); 
        
        this.styleTag = [];
        
        this.cmd("load");
        this.on("editProject",[this.id(),"editProject"]);
        inx.direct.bind(this,"handleDirect");
        
    },
    
    cmd_handleBoardChanged:function() {
        this.informer.cmd("refresh");
        this.tabs.axis("selected").cmd("load");
    },
    
    cmd_handleDirect:function(taskID) {
        if(!taskID)
            return;
        this.cmd("editTask",{taskID:taskID});
    },
    
    cmd_editTask:function(p) {
        inx({
            type:"inx.mod.board.task",
            taskID:p.taskID,
            projectID:p.projectID,
            status:p.status,
            listeners:{change:[this.tabs.axis("selected"),"load"]}
        }).cmd("render").setOwner(this);
    }, 
   
    cmd_load:function() {
        this.call({
            cmd:"board:controller:task:taskStatusList"
        },[this.id(),"handleStatuses"]);
        return false;
    },
            
    cmd_handleStatuses:function(data) {
       
        for(var i in data) {
            this.tabs.cmd("add",{
                type:"inx.mod.board.board",
                title:data[i].title,
                lazy:true,
                status:data[i].id,
                listeners:{
                    beforeload:[this.tabs.axis("head").id(),"extendLoader"],
                    load:[this.tabs.axis("head").id(),"handleTaskLoad"],
                }
            });
        }
            
        this.tabs.cmd("add",{
            type:"inx.mod.board.reports",
            title:"Отчеты",
            lazy:true
        });
        
        this.tabs.cmd("add",{
            type:"inx.mod.board.projects",
            title:"Проекты",
            lazy:true
        });
        
    },
    
    cmd_updateProjects:function() {
        this.tabs.axis("selected").cmd("load");
    },
    
    cmd_editProject:function(projectID) {
        inx({
            type:"inx.mod.board.main.editProject",
            projectID:projectID,
            listeners:{change:[this.id(),"updateProjects"]}
        }).cmd("render");
        return false;
    },
    
    cmd_highlightProject:function(projectID) {
        var style = ".qm5btw9-"+projectID+" {background:blue!important;color:white;}";
        if(this.styleTag[projectID]) {
            this.styleTag[projectID].remove();
            delete this.styleTag[projectID];
        } else {
            this.styleTag[projectID] = $("<style>").html(style).appendTo("head");
        }
    }
         
});