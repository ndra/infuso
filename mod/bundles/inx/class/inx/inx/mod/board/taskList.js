// @include inx.list,inx.panel
/*-- /board/inx.mod.board/taskList.js --*/


inx.ns("inx.mod.board").taskList = inx.list.extend({

    constructor:function(p) {    

        p.loader = {
            cmd:"board_controller_task::listTasks",
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

/*-- /board/inx.mod.board/taskList/compact.js --*/


inx.css(".webqkv2ny {table-layout:fixed;width:100%;border-collapse:collapse;}");
inx.css(".webqkv2ny td{vertical-align:middle;height:22px;padding:2;text-align:left;}");

inx.mod.board.taskList.compact = inx.panel.extend({

    constructor:function(p) {
    
        p.style = {
            
        }
        
        this.base(p);
    },
    
    cmd_render:function() {
        this.base();
        this.el.mouseenter(inx.cmd(this.id(),"showControls")).mouseleave(inx.cmd(this.id(),"hideControls"));
        
        var table = $("<table>").addClass("webqkv2ny");
        
        /*if(this.data.my) {
            table.css("border","2px solid rgb(0,0,100)");
        } */
        
        $("<col>").attr("width",40).appendTo(table);
        $("<col>").attr("width",20).appendTo(table);
        $("<col>").attr("width",100).appendTo(table);
        $("<col>").attr("width","100%").appendTo(table);
        $("<col>").attr("width",130).appendTo(table);
        
        var tr = $("<tr>").appendTo(table);
        
        var td = $("<td>").html(this.data.id).appendTo(tr);
        var td = $("<td>").html("<img src='"+this.data.data.responsibleUser.userpic+"' />").appendTo(tr);
        var td = $("<td>").html(this.data.data.status.title).appendTo(tr);        
        var td = $("<td>").html(this.data.data.text).appendTo(tr);
        this.toolsContainer = $("<td>").appendTo(tr);
        
        this.cmd("html",table);        
    },
    
    cmd_showControls:function() {
    
        if(!this.controls) {
    
            var cmp = inx({
                width:130,
                tools:this.data.data.tools,
                showMain:true,
                showAdditional:true,
                type:"inx.mod.board.taskControls",
                taskID:this.data.id,
                region:"right"
            });            
            
            this.controls = cmp;
            this.controls.cmd("render");
            this.controls.cmd("appendTo",this.toolsContainer);
            
        }
        
        this.controls.cmd("show");
        this.style("background","#ededed");
    },
    
    cmd_hideControls:function() {
        if(this.controls) {
            this.controls.cmd("hide");
        }
        this.style("background","none");
    }
     
});

/*-- /board/inx.mod.board/taskList/dateMark.js --*/



inx.mod.board.taskList.dateMark = inx.panel.extend({

    constructor:function(p) {  
        p.html = p.data.data.dateMark;  
        this.base(p);
    }
         
});

/*-- /board/inx.mod.board/taskList/task.js --*/


// Стикер задачи

inx.mod.board.taskList.task = inx.panel.extend({

    constructor:function(p) {    
        p.style = {
            border:0,
            width:130
        }
        this.base(p);
    },
    
    renderHeader:function(taskData) {
        var e = $("<div>").css({
            background:"url(" + taskData.project.icon + ") 2px center no-repeat",
            padding:"4px 0 4px 20px",
            fontWeight:"bold",
            whiteSpace:"nowrap",
            overflow:"hidden",
            textOverflow:"ellipsis",
            height:12,
            cursor:"pointer"
        }).html(taskData.id + ". " + taskData.project.title)
        .mouseenter(function() {
            $(this).css("text-decoration","underline")
        }).mouseleave(function() {
            $(this).css("text-decoration","none")
        });
        
        e.click(inx.cmd(this,"execCommand","edit"));
        
        return e;
    },
    
    renderMainControls:function(taskData,params) {
    
        var e = $("<div>");
        
        var count = taskData.tools.main.length
        var itemWidth = params.width / count;
            
        for(var i in taskData.tools.main) {
        
            var action = taskData.tools.main[i];
        
            var item = $("<div>")
                .attr("title",action)
                .css({
                    position:"absolute",
                    left:itemWidth * i,
                    width:itemWidth,
                    height:params.height,
                    color:"white",
                    cursor:"pointer",
                    background:"url(/board/res/img/icons24inverse/"+action+".png) center center no-repeat"
                }).click(inx.cmd(this,"execCommand",action))
                .appendTo(e);
                
            item.mouseenter(function() {
                $(this).css({
                    backgroundSize:"26px"
                })
            }).mouseleave(function() {
                $(this).css({
                    backgroundSize:"24px"
                })
            });
                
        }
        
        $("<div>")
            .attr("title","Редактировать")
            .css({
                position:"absolute",
                right:0,
                bottom:0,
                width:24,
                height:24,
                cursor:"pointer",
                background:"url(/board/res/img/icons16inverse/edit.png) center no-repeat"
            }).click(inx.cmd(this,"execCommand","edit")).appendTo(e);
        
        return e;
    
    },
    
    renderText:function(taskData,params) {
    
        var padding = 5;
    
        var e = $("<div>").css({
            width:params.size - 2 - padding * 2,
            height:params.height - 2 - padding * 2,
            overflow:"hidden",
            padding:padding,
            position:"relative"
        }).html(taskData.text+"");
        
        var controls = $("<div>").css({
            background:"rgba(0,0,0,.8)",
            display:"none",
            width:params.width,
            height:params.height,
            left:0,
            top:0,
            position:"absolute"
        }).appendTo(e);
        
        this.renderMainControls(taskData,params).appendTo(controls);
        
        e.mouseenter(function() {
            if(!inx.dd.enabled()) {
                controls.stop(true,true).fadeIn("fast");
            }
        }).mouseleave(function() {
            if(!inx.dd.enabled()) {
                controls.stop(true,true).fadeOut("fast");
            }
        });
        
        return e;
    },
    
    renderBody:function(taskData) {
    
        var width = this.info("width");
        var height = this.info("width");
        
        if(taskData.paused) {
            height-= 70;
        }
    
        var body = $("<div>").css({
            width:width - 2,
            height:height - 2,
            border: "1px solid rgba(0,0,0,.3)",
            position:"relative",
            boxShadow:"0 0 5px rgba(0,0,0,.3)",
            background:taskData.color || "white"
        });
        
        this.renderHeader(taskData).appendTo(body);
        
        headerHeight = 20;
        var photosHeight = (taskData.images.length  && !taskData.paused ) ? 31 : 0;

        var params = {
            width:width,
            height: height - photosHeight - headerHeight
        };
        this.renderText(taskData,params).appendTo(body);

        if(taskData.images.length && !taskData.paused) {
            var imageContainer = $("<div>").css({
                height:photosHeight,
                overflow:"hidden",
                borderTop:"1px solid rgba(0,0,0,.2)"
            }).appendTo(body);
            
            for(var i in taskData.images) {
                $("<img>")
                    .attr("src",taskData.images[i].x30)
                    .appendTo(imageContainer)
                    .data("href",taskData.images[i].original)
                    .css({
                        cursor:"pointer"
                    })
                    .click(function() {
                        window.open($(this).data("href"));
                    })
            }
        }
        
        return body;
        
    },
    
    renderStatus:function(taskData) {
    
        var e = $("<div>");
        
        $("<img>").css({
                marginRight:5
            })
            .attr("src", taskData.responsibleUser.userpic)
            .attr("align","absmiddle")
            .appendTo(e);
        
        var text = "";
        text += taskData.timeSpent;
        if(taskData.timeSpentProgress) {
            text += "+" + taskData.timeSpentProgress;
        }
        text += " / ";
        text += taskData.timeScheduled;
        
        $("<span>").css({
            }).html(text)
            .appendTo(e);
        
        return e;
    },
    
    renderFooter:function(taskData) {
    
        this.footer = $("<div>").css({
            height:30,
            borderRadius:"0 0 3px 3px"
        });
        
        this.statusContainer = $("<div>").css({
            padding:4
        }).appendTo(this.footer);
        
        this.renderStatus(taskData).appendTo(this.statusContainer)
        
        this.controlsContainer = $("<div>").css({
            padding:4,
            display:"none"
        }).appendTo(this.footer);
        
        return this.footer;
    },
    
    cmd_render:function() {
    
        this.base();
        
        this.el.css({
            overflow:"visible"
        });

        var e = $("<div>");
        
        var taskData = this.data.data;
        
        if(taskData.epic) {
            $("<img>")
                .attr("src","/board/res/img/epic-bg.png")
                .css({
                    position:"absolute",
                    left:2,
                    top:2,
                    opacity:.2
                }).appendTo(e);
        }
        
        this.renderBody(taskData).appendTo(e);
        this.renderFooter(taskData).appendTo(e);
        this.cmd("html",e);
        
        var cmp = this;
        
        this.el.mouseenter(inx.cmd(this,"showControls"));
        this.el.mouseleave(inx.cmd(this,"hideControls"));
        
        if(taskData.deadline) {
            var clock = $("<div>").css({
                position:"absolute",
                top:-10,
                right:-10,
                width:20,
                height:20,
                background:"url(/board/res/img/icons16/deadline.gif) center center no-repeat"
            }).attr("title",taskData.deadlineDate).appendTo(e);
            
            if(taskData.deadlineMissed) {
                // Делаем что-нибудь если дедлайн просрочен
            }
            
        }
        
        // Влючаем перетаскивание файлов в задачу
        inx({
            type:"inx.file",
            dropArea:this.el,
            loader:{
                cmd:"board/controller/attachment/uploadFile",
                taskID:this.data.id,
            },oncomplete:function() {
                cmp.owner().cmd("load");
            }
        }).cmd("render");
            
    },
    
    cmd_showControls:function() {
    
        if(inx.dd.enabled()) {
            return;
        }
    
        if(!this.controls) {
    
            this.controls = inx({
                width:this.info("width") - 20,
                height:this.info("height") - 20,
                tools:this.data.data.tools,
                showAdditional:true,
                type:"inx.mod.board.taskControls",
                taskID:this.data.id
            });
            
            this.controls
                .setOwner(this)
                .cmd("render")
                .cmd("appendTo",this.controlsContainer);
        }
        
        this.controlsContainer.show();
        this.statusContainer.hide();
        
    },
    
    cmd_hideControls:function() {
    
        if(inx.dd.enabled()) {
            return;
        }
        
        this.controlsContainer.hide();
        this.statusContainer.show();
    
    },
    
    cmd_execCommand:function(cmd) {
    
        if(inx.dd.enabled()) {
            return;
        }
        
        this.controls.cmd("execCommand",cmd);
    }

         
});

