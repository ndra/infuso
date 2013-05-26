// @include inx.viewport,inx.tabs,inx.direct

inx.ns("inx.mod.board").main = inx.viewport.extend({

    constructor:function(p) {
    
        p.style = {
            border:0
        }
    
        layout = "inx.layout.fit";
    
        this.tabs = inx({
            type:"inx.tabs",
            showHead:false,
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
    
    cmd_refreshTaskList:function() {
        this.tabs.axis("selected").cmd("load");
    },
    
    cmd_handleBoardChanged:function() {
        this.informer.cmd("refresh");
        this.tabs.axis("selected").cmd("load");
    },
    
    cmd_handleDirect:function(params) {
    
        switch(params.action) {
            
            case "task-list":
                this.tabs.cmd("add",{
                    type:"inx.mod.board.board",
                    status:params.params.status,
                    title:"task-list"+params.params.status,
                    name:"task-list"+params.params.status
                });
                break;
                

            case "report-workers":
                this.tabs.cmd("add",{
                    type:"inx.mod.board.report.workers",
                    name:"report-workers"
                });
                break;
                
            case "report-projects":
                this.tabs.cmd("add",{
                    type:"inx.mod.board.report.projects",
                    name:"report-projects"
                });
                break;
                
            case "report-project":
                this.tabs.cmd("add",{
                    type:"inx.mod.board.report.project",
                    projectID:params.params.id,
                    name:"report-project/"+params.params.id
                });
                break;
                
        }

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
    
    cmd_updateProjects:function() {
        this.tabs.axis("selected").cmd("load");
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