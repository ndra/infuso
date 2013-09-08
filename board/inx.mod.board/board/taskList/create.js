// @link_with_parent


inx.mod.board.board.taskList.create = inx.panel.extend({

    constructor:function(p) {    
        p.style = {
            padding:15
        };
        this.base(p);
    },
    
    cmd_render:function() {
    
        var e = $("<div>").css({
            whiteSpace:"nowrap"
        });
        
        $("<div>")
            .css({
                display:"inline-block",
                fontSize:16,
                marginRight:10
            }).html("<b>Новая задача</b>")
            .appendTo(e);
        
        for(var i=0;i<10;i++) {
            $("<div>")
                .css({
                    display:"inline-block",
                    fontSize:16,
                    marginRight:10
                }).html("Зеленский")
                .appendTo(e);
        }
    
        this.base();
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