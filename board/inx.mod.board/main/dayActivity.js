// @link_with_parent

inx.mod.board.main.dayActivity = inx.panel.extend({

    constructor:function(p) {
    
        p.style = {
            background:"#ededed"
        }
        p.height = 20;        
        this.on("render","getDayActivity");
        this.base(p);
        
        this.extend({
            getMainComponent:function() {
                return inx(this).axis("parents").eq("type","inx.mod.board.main");
            }
        })
        
    },
    
    cmd_getDayActivity:function() {
        this.call({
            cmd:"board/controller/report/getMyDayActivity"
        },[this.id(),"handleData"]);
        
        // Обновляем раз в пять минут
        this.task("getDayActivity",1000 * 60*5);
    },
    
    cmd_handleData:function(data) {
        var e = $("<div>");
        
        var k = this.info("bodyWidth") / 3600 / 24;
        
        for(var i=0;i<24;i++) {
            $("<div>").css({
                position:"absolute",
                left:3600 * i * k,
                top:4,
                opacity:.5
            }).html(i).appendTo(e);
        }
    
        var time = new Date().getSeconds() + (new Date).getMinutes()*60 + (new Date).getHours()*3600;
        $("<div>").css({
            position:"absolute",
            left:time * k,
            top:0,
            width:1,
            height:20,
            background:"black"
        }).appendTo(e);
        
        var cmp = this;
        
        for(var i in data.tasks) {
            var task = data.tasks[i];
            
            $("<div>").css({
                position:"absolute",
                height:20,
                background:"rgba(0,0,0,.5)",
                width:task.duration * k,
                left:task.start * k
            }).attr("title",task.title)
                .data("taskID",task.taskID)
                .click(function() {
                    var taskID = $(this).data("taskID");
                    cmp.getMainComponent().cmd("editTask",{taskID:taskID});
                })
                .appendTo(e);
        }
        
        this.cmd("html",e);
    }
         
});