// @link_with_parent
// @include inx.list

inx.mod.board.task.subtasks = inx.list.extend({

    constructor:function(p) {
    
        p.style = {
            border:0,
            padding:0,
            maxHeight:300,
            background:"none"
        };
        
        p.sortable = true;
        
        p.loader = {
            cmd:"board/controller/task/getEpicSubtasks",
            taskID:p.taskID
        };
        
        p.side = [{
            type:p.type+".add",
            taskID:p.taskID,
            region:"bottom",
            listeners:{
                subtaskAdded:[this.id(),"load"]
            }
        }]
        
        this.base(p);
        
    },
    
    cmd_handleItemMouseOver:function(e,data) {
        if(!e.data("bnfgh3-controls")) {
        
            var cmp = this;
        
            var controls = $("<div>")
                .css({
                    position:"absolute",
                    right:0,
                    top:0
                })
                .appendTo(e);
                
            $("<div>").css({
                position:"absolute",
                width:16,
                height:16,
                right:16,
                top:0,
                background:"url("+inx.img("ok")+")"
            }).click(function() {
                cmp.cmd("completeEpicSubtask",data.id);
            }).appendTo(controls);
            
            $("<div>").css({
                position:"absolute",
                width:16,
                height:16,
                right:0,
                top:0,
                background:"url("+inx.img("delete")+")"
            }).click(function() {
                cmp.cmd("cancelEpicSubtask",data.id);
            }).appendTo(controls);
        
            e.data("bnfgh3-controls",controls);
        }
        
        e.data("bnfgh3-controls").stop(true,true).fadeIn("fast");
        
        var cmp = this;
        e.mouseleave(function() {
            cmp.cmd("handleItemMouseOut",e);
        })
    },
    
    cmd_handleItemMouseOut:function(e) {
        e.data("bnfgh3-controls").stop(true,true).fadeOut("fast");
    },
    
    renderer:function(e,data) {
    
        // Текст задачи
        var text = $("<div>")
            .html(data.text+"")
            .appendTo(e);
            
        if(data.completed) {
            text.css({
                textDecoration:"line-through"
            });
        }
            
        $("<div>")
            .html(data.timeScheduled)
            .appendTo(e);
            
        var cmp = this;
        e.mouseenter(function() {
            cmp.cmd("handleItemMouseOver",e,data);
        })
            
    },
    
    /**
     * Завершение подзадачи. Открывает окно с подтверждением
     **/
    cmd_completeEpicSubtask:function(taskID) {
    
        var h = window.prompt("Сколько было потрачено времени?");
    
        if(h===null) {
            return;
        }
        
        this.call({
            cmd:"board/controller/task/changeTaskStatus",
            taskID:taskID,
            status:2,
            time:h
        },[this.id(),"load"])
        
    },
    
    /**
     * Удаление подзадачи. Открывает окно с подтверждением
     **/
    cmd_cancelEpicSubtask:function(taskID) {
    
        var h = window.prompt("удалить подзадачу?");
    
        if(h===null) {
            return;
        }
        
        this.call({
            cmd:"board/controller/task/changeTaskStatus",
            taskID:taskID,
            status:100,
            time:h
        },[this.id(),"load"])
        
    }
     
});