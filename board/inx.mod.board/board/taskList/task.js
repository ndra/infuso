// @include inx.list
// @link_with_parent

// Стикер задачи

inx.css(".qm5btw9-task{font-family:verdana;font-size:11px;}");
inx.css(".qm5btw9{overflow:hidden;position:relative;cursor:pointer;background:white;box-shadow:0px 0px 5px rgba(0, 0, 0, 0.1);}");

inx.css(".qm5btw9-status{position:absolute;bottom:0;left:0;width:100%;color:black;padding:2px 1px 1px 2px;white-space:nowrap;}");
inx.css(".qm5btw9-hover-group .qm5btw9-background{background:rgba(255,255,0,.2);}");
inx.css(".qm5btw9-my {border:1px solid blue;}")

inx.mod.board.board.taskList.task = inx.panel.extend({

    constructor:function(p) {    
        p.style = {
            border:0,
            height:130,
            width:130
        }
        this.base(p);
    },
    
    cmd_render:function() {
    
        var width = this.info("width");
        var height = this.info("height");
    
        this.base();

        var task = this.data.data;
  
        // Отметка даты
        if (this.data.dateMark) {
            $("<div class='qm5btw9-date-mark' >")
                .appendTo(this.el)
                .html(this.data.dateMark);
            this.style("break",true).style("width","parent").style("height",10);
                
        } else {
        
            this.el.css({overflow:"visible"});
    
            // При наведении на задачу, подсвечиваем все задачи из того же проекта
            var taskContainer = $("<div>")
                .addClass("qm5btw9-task")
                .data("taskID",task.id);
          
            var e = $("<div>")
                .addClass("qm5btw9")
                .css({
                    height:height - 12,
                    padding:6
                })
                .appendTo(taskContainer)
                .addClass("qm5btw9-"+task.projectID);
                
            if(!task.color || task.color=="#ffffff") {
                e.css({
                    padding:5,
                    border:"1px solid #ccc"
                });
            }
            
            // Подсвечиваем свои задачи    
            if(task.my) {
                e.addClass("qm5btw9-my");
            }    
            
            // Цвет листика
            if(task.color) {
                e.css({
                    background:task.color
                });
            }
            
            if(!task.paused) {
                $("<div>").css({
                    width:width,
                    height:height,
                    position:"absolute",
                    left:0,
                    top:0,
                    opacity:.3,
                    background:"url(/board/res/img/icons64/resume.png) center center no-repeat"
                }).appendTo(e);
            }
            
            // Иконка, id задачи и проект 
            var head = $("<div>")
                .css({
                    background:"url(" + task.project.icon + ") left center no-repeat",
                    padding:"3px 0 3px 20px",
                    fontFamily:"georgia",
                    fontStyle:"italic",
                    fontWeight:"bold"
                }).appendTo(e);
                
            $("<span>")
                .css({
                    whiteSpace:"nowrap"
                }).html(task.project.title)
                .appendTo(head);
            
            // Контейнер текста
            var text = $("<div>")
                .html(task.text)
                .css({
                    height:height-23,
                    overflow:"hidden",
                    position:"relative"
                }).appendTo(e);

            // Статус листика
            
            var status = "";
            status += task.timeSpent + " из " + task.timeScheduled + "ч."; 
    
            var bottom = $("<div>")
                .html(status)
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
                
            // Вешаем обработчики на наведение / уход мыши со стикера
            
            this.el.mouseenter(function() {
                cmp.cmd("showControls");
            }).mouseleave(function() {
                cmp.cmd("hideControls");
            });
            
            this.toolsContainer = $("<div>").css({
                width:width-20,
                height:height-20,
                padding:10,
                position:"absolute",
                left:0,
                top:0,
                background:"rgba(0,0,0,.85)",
                display:"none"
            }).appendTo(e);
                
            this.cmd("html",taskContainer);
        
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
    
        this.toolsContainer.stop(true,true).fadeIn(300);
        
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
                .cmd("appendTo",this.toolsContainer);
        }
        
    },
    
    cmd_hideControls:function() {
        this.toolsContainer.stop(true,true).fadeOut(300);
    },
    

         
});