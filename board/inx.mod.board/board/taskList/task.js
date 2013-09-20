// @include inx.list
// @link_with_parent

// Стикер задачи

inx.mod.board.board.taskList.task = inx.panel.extend({

    constructor:function(p) {    
        p.style = {
            border:0,
            width:130
        }
        this.base(p);
    },
    
    renderBody:function(taskData) {
    
        var size = this.info("width");
    
        var body = $("<div>").css({
            width:size-2,
            height:size-2,
            border:"1px solid #ccc",
            position:"relative",
            boxShadow:"0 0 5px rgba(0,0,0,.3)",
            background:"white"
        });
        
        var padding = 10;
        var photosHeight = taskData.images.length ? 30 : 0;
        var textHeight = size-photosHeight;

        $("<div>").css({
            width:size - 2 - padding * 2,
            height:textHeight - 2 - padding * 2,
            overflow:"hidden",
            padding:padding
        }).html(taskData.text+"")
        .appendTo(body);

        if(taskData.images) {
            var imageContainer = $("<div>").css({
                height:photosHeight,
                background:"#ccc",
                overflow:"hidden"
            }).appendTo(body);
            
            for(var i in taskData.images) {
                $("<img>")
                    .attr("src",taskData.images[i].x30)
                    .appendTo(imageContainer)
            }
        }
        
        return body;
        
    },
    
    renderFooter:function() {
        this.footer = $("<div>").css({
            height:30,
            background:"rgba(0,150,255,.2)",
            borderRadius:"0 0 3px 3px"
        });
        
        this.controlsContainer = $("<div>").css({
            padding:4
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
        
        this.renderBody(taskData).appendTo(e);
        this.renderFooter(taskData).appendTo(e);
        this.cmd("html",e);
        
        var cmp = this;
        
        this.el.mouseenter(inx.cmd(this,"showControls"))
        this.el.mouseleave(inx.cmd(this,"hideControls"))
        
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
                type:"inx.mod.board.taskControls",
                taskID:this.data.id
            });
            
            this.controls
                .cmd("render")
                .cmd("appendTo",this.controlsContainer);
        }
        
        this.controlsContainer.show();
        
    },
    
    cmd_hideControls:function() {
    
        if(inx.dd.enabled()) {
            return;
        }
        
        this.controlsContainer.hide();
    
    },
    

         
});