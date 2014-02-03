// @link_with_parent

inx.mod.board.main.dayActivity.user = inx.panel.extend({

    constructor:function(p) {
    
        if(!p.style) {
            p.style = {}
        }
        p.style.background = "#ededed";
        
        p.height = 20;        
        this.on("render","getDayActivity");
        
        this.user = inx({
            type:"inx.panel",
            region:"left",
            width:20
        });
        
        p.side = [this.user];
        
        this.base(p);        
        
        this.extend({
            getMainComponent:function() {
                return inx(this).axis("parents").eq("type","inx.mod.board.main");
            }
        });
        
        this.on("click",function() {
            this.owner().fire("click");
        });
        
    },
    
    cmd_getDayActivity:function() {
    
        if(this.info("visibleRecursive")) {
            this.call({
                cmd:"infuso/board/controller/report/getMyDayActivity",
                userID:this.userID
            },[this.id(),"handleData"]);
        }
        
        // Обновляем раз в пять минут
        this.task("getDayActivity",1000 * 60*5);
    },
    
    cmd_handleData:function(data) {
        this.data = data;
        this.cmd("renderActivity");
    },
    
    cmd_syncLayout:function() {
        this.base();
        this.task("renderActivity",1000);
    },
    
    cmd_renderActivity:function() {
    
        var data = this.data;
        if(!data) {
            return;
        }
    
        var e = $("<div>");
        
        var k = this.info("bodyWidth") / 3600 / 24;
        
        if(this.showHours) {
            for(var i=0;i<24;i++) {
                $("<div>").css({
                    position:"absolute",
                    left:3600 * i * k,
                    fontSize:10,
                    top:4,
                    opacity:.5
                }).html(i).appendTo(e);
            }
        }
    
        // Текущее время
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
        
        //  Задачи по пользователям
        for(var i in data.tasks) {
            var task = data.tasks[i];
            
            var te = $("<div>").css({
                position:"absolute",
                height:20,
                background:i%2 ? "rgba(0,0,0,.5)" : "rgba(0,0,0,.7)",
                width:task.duration * k,
                left:task.start * k
            }).attr("title",task.title)
            .data("taskID",task.taskID)
            .click(function(e) {
                e.stopPropagation();
                var taskID = $(this).data("taskID");
                cmp.getMainComponent().cmd("editTask",{taskID:taskID});
            })
            .appendTo(e);
            
            if(task.inprogress) {
                te.css({
                    background:"rgba(0,0,125,.5)"
                })
            }
            
        }
        
        var user = $("<img>")
            .attr("src",data.user.userpick20);
        this.user.cmd("html",user);
        
        this.cmd("html",e,{syncLayout:false});
    }
         
});