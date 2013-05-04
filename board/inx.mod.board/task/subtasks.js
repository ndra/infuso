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
                subtaskAdded:[this.id(),"handleChanges"]
            }
        }]
        
        this.base(p);
        
        this.on("sortcomplete",[this.id(),"handleSortComplete"]);
        
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

            $("<div title='Буду делать' >").css({
                position:"absolute",
                width:16,
                height:16,
                right:40,
                top:0,
                cursor:"pointer",
                background:"url(/board/res/img/icons16/runner.png)"
            }).click(function() {
                cmp.cmd("doEpicSubtask",data.id);
            }).appendTo(controls);
            
            $("<div>").css({
                position:"absolute",
                width:16,
                height:16,
                right:20,
                top:0,
                cursor:"pointer",
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
            
            var cmp = this;
            e.mouseleave(function() {
                cmp.cmd("handleItemMouseOut",e);
            })
        }
        
        e.data("bnfgh3-controls").stop(true,true).fadeIn("fast");
        e.data("time").stop(true,true).fadeOut("fast");
    },
    
    cmd_handleItemMouseOut:function(e) {
        e.data("bnfgh3-controls").stop(true,true).fadeOut("fast");
        e.data("time").stop(true,true).fadeIn("fast");
    },
    
    renderer:function(e,data) {
    
        e.css({
            paddingRight:50
        });
    
        // Текст задачи
        var text = $("<div>")
            .html(data.text+"")
            .appendTo(e);
            
        if(data.completed) {
            text.css({
                textDecoration:"line-through"
            });
        }
            
        var time = $("<div>")
            .css({
                position:"absolute",
                right:0,
                top:0
            })
            .html(data.timeScheduled)
            .appendTo(e);
            
        e.data("time",time);
            
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
        },[this.id(),"handleChanges"])
        
    },
    
    /**
     * Удаление подзадачи. Открывает окно с подтверждением
     **/
    cmd_cancelEpicSubtask:function(taskID) {
    
        if(!window.confirm("удалить подзадачу?")) {
            return;
        }
        
        this.call({
            cmd:"board/controller/task/changeTaskStatus",
            taskID:taskID,
            status:100
        },[this.id(),"handleChanges"])
        
    },
    
    /**
     * Посылает команду «Я буду делать задачу»
     **/
    cmd_doEpicSubtask:function(taskID) {
        
        this.call({
            cmd:"board/controller/task/changeTaskStatus",
            taskID:taskID,
            status:1
        },[this.id(),"handleChanges"])
        
    },
    
    cmd_handleChanges:function() {
        this.fire("change");
        this.cmd("load");
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