// @include inx.list
// @link_with_parent

// Стикер задачи

inx.css(".qm5btw9-task{}");

//inx.css(".qm5btw9{overflow:hidden;position:relative;cursor:pointer;height:100%;border:1px solid #cccccc;box-shadow:0px 0px 5px rgba(0, 0, 0, 0.1);}");
//inx.css(".qm5btw9:hover{border:1px solid gray;}");
inx.css(".qm5btw9-status{position:absolute;bottom:0;left:0;width:100%;background:rgba(0,0,0,.2);color:white;padding:2px 1px 1px 2px;fontWeight:bold}");

inx.css(".v07pe2tku{font-size:14px;cursor:pointer;padding:5px;display:inline-block;}");
//inx.css(".v07pe2tku:hover{background:#ededed;border-radius:4px;}");
inx.css(".v07pe2tku-info{color:#ccc;font-size:11px;}");

//inx.css(".qm5btw9-hover-group .qm5btw9-background{background:rgba(0,0,0,.2);}");

inx.mod.board.board.taskList.task = inx.box.extend({

    constructor:function(p) {    
        p.width = 100;
        p.height = 100;
        this.base(p);
    },
    
    cmd_render:function() {
    
        this.base();
    
        var task = this.data.data;
    
        var taskContainer = $("<div>")
            .addClass("qm5btw9-task")
            .data("taskID",task.id)
            .mouseover(function() {
                $(".qm5btw9-"+task.projectID).addClass("qm5btw9-hover-group");
            }).mouseout(function() {
                $(".qm5btw9-"+task.projectID).removeClass("qm5btw9-hover-group");
            });
            
        if(task.sort)
            inx.dd.enable(taskContainer,this,"handleDD");
    
        var e = $("<div>")
            .addClass("qm5btw9")
            .appendTo(taskContainer)
            .addClass("qm5btw9-"+task.projectID);
            
        if(task.my)
            e.addClass("qm5btw9-my");
        
        // Цвет листика
        if(task.color)
            e.css({background:task.color});
            
        if(task.deadline)
            e.css({"background-image":"url(/board/res/task-time.png)"});
            
        if(task.fuckup)
            e.css({"background-image":"url(/board/res/task-time-fuckup.png)"});
            
        var e = $("<div>").css({height:100}).addClass("qm5btw9-background").appendTo(e);
            
        e.click(inx.cmd(this,"editTask",{taskID:task.id}));
        $("<div>").css({height:77,padding:4,overflow:"hidden"}).html(task.text+"").appendTo(e);

        // Статус листика
        $("<div>").html(task.info+"").appendTo(e).addClass("qm5btw9-status");    
        
        if(task.bottom)
            $("<div>").html(task.bottom+"").css({marginTop:4,opacity:.5}).appendTo(taskContainer);
            
            
        taskContainer.appendTo(this.el)
            
    }

         
});