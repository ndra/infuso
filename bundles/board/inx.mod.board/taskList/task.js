// @include inx.list
// @link_with_parent

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
                    background:"url("+inx.conf.boardRes+"img/icons24inverse/"+action+".png) center center no-repeat"
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