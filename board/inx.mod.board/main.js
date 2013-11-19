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
            width:400,
            resizable:true
        });
        
        this.dayActivity = inx({
            type:"inx.mod.board.main.dayActivity",
            region:"bottom"
        });
        
        p.side = [this.informer,this.dayActivity,this.messages];
        
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
                }).cmd("setParams",params.params);
                break;
                
            case "report-project":
                this.tabs.cmd("add",{
                    type:"inx.mod.board.report.project",
                    projectID:params.params.id,
                    name:"report-project/"+params.params.id
                }).cmd("setParams",params.params);
                break;
                
            case "report-vote":
                this.tabs.cmd("add",{
                    type:"inx.mod.board.report.vote",
                    name:"report-vote"
                }).cmd("setParams",params.params);
                break;
                
            case "report-done":
                this.tabs.cmd("add",{
                    type:"inx.mod.board.report.done",
                    name:"report-done"
                }).cmd("setParams",params.params);
                break;
                
            case "report-gallery":
                this.tabs.cmd("add",{
                    type:"inx.mod.board.report.gallery",
                    name:"report-gallery"
                }).cmd("setParams",params.params);
                break;
                
            case "report-chart":
                this.tabs.cmd("add",{
                    type:"inx.mod.board.report.chart",
                    name:"report-gallery"
                }).cmd("setParams",params.params);
                break;
                
            case "conf-access":
                this.tabs.cmd("add",{
                    type:"inx.mod.board.access",
                    name:"access"
                }).cmd("setParams",params.params);
                break;
                
            case "conf-projects":
                this.tabs.cmd("add",{
                    type:"inx.mod.board.projects",
                    name:"projects"
                }).cmd("setParams",params.params);
                break;
                
            case "conf-profile":
                this.tabs.cmd("add",{
                    type:"inx.mod.board.profile",
                    name:"profile"
                }).cmd("setParams",params.params);
                break;
                
            case "messages":
                this.tabs.cmd("add",{
                    type:"inx.mod.board.messages",
                    name:"messages"
                }).cmd("setParams",params.params);
                break;
                
            case "task":
                this.cmd("editTask",{taskID:params.params.id});
                if(!params.first) {
                    history.back();
                }
                break;
                
            case "vote":
                this.cmd("voteTask",{taskID:params.params.id});
                history.back();
                break;     
                
            case "tags":
                this.cmd("editTags",{taskID:params.params.id});
                history.back();
                break;   
                           
        }

    },
    
    cmd_editTask:function(p) {
        inx({
            type:"inx.mod.board.task",
            taskID:p.taskID,
            projectID:p.projectID,
            status:p.status
        }).cmd("render").setOwner(this);
    }, 
    
    cmd_voteTask:function(p) {
        inx({
            type:"inx.dialog",
            destroyOnEscape:true,
            width:500,
            title:"Оцените задачу",
            style:{
                border:0,
                background:"white",
            },items:[{
                type:"inx.mod.board.vote",
                taskID:p.taskID
            }]
        }).cmd("render").setOwner(this);
    }, 
    
    /**
     * Открывает диалог редактирования тэгов
     **/
    cmd_editTags:function(p) {
        inx({
            type:"inx.mod.board.tagEditor",
            taskID:p.taskID
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