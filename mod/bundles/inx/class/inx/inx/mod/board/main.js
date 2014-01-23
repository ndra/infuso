// @include inx.viewport,inx.tabs,inx.direct,inx.mod.board.board
/*-- /board/inx.mod.board/main.js --*/


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

/*-- /board/inx.mod.board/main/dayActivity.js --*/


inx.mod.board.main.dayActivity = inx.panel.extend({

    constructor:function(p) {
    
        p.style = {
            background:"#ededed"
        }
        
        this.base(p);
        this.on("click","toggle");
        
        this.cmd("add",{
            type:this.info("type")+"."+"user",
            name:"me",
            showHours:true,
            style:{
                border:0
            }
        });
        
    },
    
    cmd_toggle:function() {
        if (this.expanded) {
            this.cmd("collapse");
        } else {
            this.cmd("expand");
        }
    },
    
    /**
     * Разворачивает панель
     **/
    cmd_expand:function() {
        this.expanded = true;
        this.cmd("loadUsers");
        this.items().neq("name","me").cmd("show");
    },
    
    /**
     * Сворачивает панель
     **/
    cmd_collapse:function() {
        this.expanded = false;
        this.items().neq("name","me").cmd("hide");
    },
    
    cmd_loadUsers:function() {
        if(this.usersLoaded) {
            return;
        }
        this.usersLoaded = true;
        this.call({
            cmd:"board/controller/report/getUsers"
        },[this.id(),"handleUsers"]);
    },
    
    cmd_handleUsers:function(data) {
        for(var i in data) {
            this.cmd("add",{
                type:this.info("type")+"."+"user",
                userID:data[i].userID,
                style:{
                    border:0
                }
            });
        }
    }
         
});

/*-- /board/inx.mod.board/main/dayActivity/user.js --*/


inx.mod.board.main.dayActivity.user = inx.panel.extend({

    constructor:function(p) {
    
        if(!p.style) {
            p.style = {}
        }
        p.style.background = "#ededed";
        
        p.height = 20;        
        this.on("render","getDayActivity");
        
        this.user = inx({
            type:"inx.panel",
            region:"left",
            width:20
        });
        
        p.side = [this.user];
        
        this.base(p);        
        
        this.extend({
            getMainComponent:function() {
                return inx(this).axis("parents").eq("type","inx.mod.board.main");
            }
        });
        
        this.on("click",function() {
            this.owner().fire("click");
        });
        
    },
    
    cmd_getDayActivity:function() {
    
        if(this.info("visibleRecursive")) {
            this.call({
                cmd:"board/controller/report/getMyDayActivity",
                userID:this.userID
            },[this.id(),"handleData"]);
        }
        
        // Обновляем раз в пять минут
        this.task("getDayActivity",1000 * 60*5);
    },
    
    cmd_handleData:function(data) {
        this.data = data;
        this.cmd("renderActivity");
    },
    
    cmd_syncLayout:function() {
        this.base();
        this.task("renderActivity",1000);
    },
    
    cmd_renderActivity:function() {
    
        var data = this.data;
        if(!data) {
            return;
        }
    
        var e = $("<div>");
        
        var k = this.info("bodyWidth") / 3600 / 24;
        
        if(this.showHours) {
            for(var i=0;i<24;i++) {
                $("<div>").css({
                    position:"absolute",
                    left:3600 * i * k,
                    fontSize:10,
                    top:4,
                    opacity:.5
                }).html(i).appendTo(e);
            }
        }
    
        // Текущее время
        var time = new Date().getSeconds() + (new Date).getMinutes()*60 + (new Date).getHours()*3600;
        $("<div>").css({
            position:"absolute",
            left:time * k,
            top:0,
            width:1,
            height:20,
            background:"black"
        }).appendTo(e);
        
        var cmp = this;
        
        //  Задачи по пользователям
        for(var i in data.tasks) {
            var task = data.tasks[i];
            
            var te = $("<div>").css({
                position:"absolute",
                height:20,
                background:i%2 ? "rgba(0,0,0,.5)" : "rgba(0,0,0,.7)",
                width:task.duration * k,
                left:task.start * k
            }).attr("title",task.title)
            .data("taskID",task.taskID)
            .click(function(e) {
                e.stopPropagation();
                var taskID = $(this).data("taskID");
                cmp.getMainComponent().cmd("editTask",{taskID:taskID});
            })
            .appendTo(e);
            
            if(task.inprogress) {
                te.css({
                    background:"rgba(0,0,125,.5)"
                })
            }
            
        }
        
        var user = $("<img>")
            .attr("src",data.user.userpick20);
        this.user.cmd("html",user);
        
        this.cmd("html",e,{syncLayout:false});
    }
         
});

/*-- /board/inx.mod.board/main/informer.js --*/


inx.mod.board.main.informer = inx.panel.extend({

    constructor:function(p) {
    
        p.style = {
            padding:15,
            spacing:10,
            vscroll:true
        }
   
        p.items = [{
            type:"inx.mod.board.taskList",
            status:1,
            style:{
                border:0,
                padding:0
            }
        },{
            type:"inx.mod.board.comments",
            style:{
                padding:1,
                border:0
            }
        }];
    
        this.base(p);
    }
         
});

