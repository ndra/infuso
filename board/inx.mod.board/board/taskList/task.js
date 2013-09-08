// @include inx.list
// @link_with_parent

// Стикер задачи

inx.css(".qm5btw9-task{font-size:11px;}");
inx.css(".qm5btw9{overflow:hidden;position:relative;cursor:pointer;height:100%;background:white;border:1px solid #cccccc;box-shadow:0px 0px 5px rgba(0, 0, 0, 0.1);}");
inx.css(".qm5btw9:hover{border:1px solid gray;}");
inx.css(".qm5btw9-status{position:absolute;bottom:0;left:0;width:100%;background:rgba(0,0,0,.2);color:white;padding:2px 1px 1px 2px;fontWeight:bold}");
inx.css(".qm5btw9-hover-group .qm5btw9-background{background:rgba(255,255,0,.2);}");
inx.css(".qm5btw9-my {outline:2px solid blue;}")

inx.mod.board.board.taskList.task = inx.panel.extend({

    constructor:function(p) {    
        p.style = {
            border:0,
            height:120,
            width:100
        }
        this.base(p);
    },
    
    cmd_render:function() {
    
        this.base();
        this.el.css({overflow:"visible"})

        var task = this.data.data;
  
        // Отметка даты
        if (this.data.dateMark) {
            $("<div class='qm5btw9-date-mark' >")
                .appendTo(this.el)
                .html(this.data.dateMark);
            this.style("break",true).style("width","parent").style("height",10);
                
        } else {
    
            // При наведении на задачу, подсвечиваем все задачи из того же проекта
            var taskContainer = $("<div>")
                .addClass("qm5btw9-task")
                .data("taskID",task.id);
                
            if(task.epic) {
                
                $("<div>")
                    .css({
                        width:98,
                        height:100,
                        border:"1px solid rgba(0,0,0,.2)",
                        background:"white",
                        position:"absolute",
                        left:4,
                        top:4
                    })
                    .appendTo(taskContainer);
                    
                $("<div>")
                    .css({
                        width:98,
                        height:100,
                        border:"1px solid rgba(0,0,0,.2)",
                        background:"white",
                        position:"absolute",
                        left:2,
                        top:2
                    })
                    .appendTo(taskContainer);
            }   
        
            var e = $("<div>")
                .addClass("qm5btw9")
                .appendTo(taskContainer)
                .addClass("qm5btw9-"+task.projectID);
            
            // Подсвечиваем свои задачи    
            if(task.my) {
                e.addClass("qm5btw9-my");
            }    
            
            // Цвет листика
            if(task.color) {
                e.css({background:task.color});
            }

            var e = $("<div>").css({height:100}).addClass("qm5btw9-background").appendTo(e);
                
            e.click(inx.cmd(this,"editTask",{taskID:task.id}));
            $("<div>").css({height:77,padding:4,overflow:"hidden"}).html(task.text+"").appendTo(e);
    
            // Статус листика
            var bottom = $("<div>")
                .html("ололо!")
                .appendTo(e)
                .addClass("qm5btw9-status");  
            
            if(task.attachment) {
                $("<img src='/board/res/img/icons16/attachment.png' />")
                    .css({
                        position:"absolute",
                        right:4,
                        top:0
                    }).appendTo(bottom);
            }
            
            /*var cmp = this;
            var controls = $("<div>")
                .addClass("controls")
                .css({
                    background:"#666",
                    position:"relative",
                    padding:10,
                    zIndex:1
                }).appendTo(taskContainer); */
                
            taskContainer.mouseenter(function() {
                cmp.cmd("showControls");
            });
             taskContainer.mouseleave(function() {
                cmp.cmd("hideControls");
            });
                
            this.cmd("html",taskContainer)
        
        }
        
        var cmp = this;
        
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
        
        if(!this.controls) {
    
            var cmp = inx({
                tools:this.data.data.tools,
                type:"inx.mod.board.taskControls",
                taskID:this.data.id
            });
            
            this.controls = cmp;
        }
        
        clearTimeout(this.hideControlsTimeout);
        
        if(this.controlsDlg) {
            return;
        }
        
        this.controlsDlg = inx({
            type:"inx.dialog",
            modal:false,
            clipToOwner:true,
            showTitle:false,
            autoDestroy:true,
            items:[this.controls],
            style:{
                width:200,
                height:100,
                background:"#666",
                padding:10,
                color:"white"
            }
        }).cmd("render").setOwner(this);
        
        var e = this.controlsDlg.info("param","el");
        
        var cmp = this;
        e.mouseenter(function() {
            cmp.cmd("showControls");
        }).mouseleave(function() {
            cmp.cmd("hideControls");
        });
        
    },
    
    cmd_hideControls:function() {
        this.hideControlsTimeout = setTimeout(inx.cmd(this,"realHideControls"),0);
    },
    
    cmd_realHideControls:function() {
    
        if(!this.controlsDlg) {
            return;
        }
        //this.controlsDlg.items().cmd("remove");
        this.controlsDlg.cmd("destroy");
        this.controlsDlg = null;
    }
    

         
});