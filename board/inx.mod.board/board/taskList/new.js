// @include inx.panel
// @link_with_parent

// Новая задача
inx.css(".yazn8ghncl2-new{width:100px;height:100px;cursor:pointer;}");
inx.css(".yazn8ghncl2-bg{position:absolute;left:0;top:0;width:100px;height:100px;cursor:pointer;background:url(/board/res/img/icons64/plus.png) center center no-repeat;}");
inx.css(".yazn8ghncl2-new:hover{}");

inx.css(".yazn8ghncl2-project{position:relative;white-space:nowrap;}");
inx.css(".yazn8ghncl2-project:hover{background:green;color:white;}");

inx.mod.board.board.taskList["new"] = inx.panel.extend({

    constructor:function(p) {    
        p.style = {
            border:0,
            width:100
        }
        this.base(p);
    },
    
    cmd_render:function() {
        this.base();
        var e = $("<div class='yazn8ghncl2-new' >");
        var bg = $("<div>")
            .addClass("yazn8ghncl2-bg")
            .appendTo(e);
        var projectContainer = $("<div>")
            .css({
                position:"relative",
                opacity:0
            }).appendTo(e);
            
        e.mouseenter(function() {
            bg.stop(true,true).animate({
                opacity:.1
            });
            projectContainer.stop(true,true).animate({
                opacity:1
            });
        });
        
        e.mouseleave(function() {
            bg.stop(true,true).animate({
                opacity:1
            });
            projectContainer.stop(true,true).animate({
                opacity:0
            });
        });
            
        for(var i in this.data.recentProjects) {
            var project = this.data.recentProjects[i];
            $("<div>")
                .html(project.title)
                .addClass("yazn8ghncl2-project")
                .appendTo(projectContainer)
                .click(inx.cmd(this,"newTask",project.id));
        }
        this.cmd("html",e);
    },
    
    cmd_newTask:function(projectID) {
        this.call({
            cmd:"board/controller/task/newTask",
            projectID:projectID
        },[this.id(),"handleCreateNewTask"]);
    },
    
    cmd_handleCreateNewTask:function(data) {
        if(!data) {
            return;
        }
        window.location.hash = "task/id/" + data;
    }
         
});