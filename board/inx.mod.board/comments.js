// @include inx.list

inx.ns("inx.mod.board").comments = inx.list.extend({

    constructor:function(p) {
    
        if(!p.style) {
            p.style = {};
        }
        p.style.background = "#F5F5EA";
    
        p.loader = {
            cmd:"board/controller/log/getLog",
            taskID:p.taskID
        }
        
        p.side = [{
            type:p.type+".controls",
            name:"controls",
            region:"top",
            listeners:{
                change:[this.id(),"load"]
            }
        },{
            type:p.type+".send",
            region:"bottom",
            taskID:p.taskID,
            listeners:{
                send:[this.id(),"load"]
            }
        }];
        
        this.base(p);
        setInterval(inx.cmd(this.id(),"load"),1000*60);
        
        this.on("itemclick",[this.id(),"handleItemClick"]);
        this.on("beforeload",[this.id(),"handleBeforeLoad"]);
    },
    
    cmd_handleBeforeLoad:function(loader) {
        loader.mode = inx(this).axis("side").eq("name","controls").info("value");
    },
    
    renderer:function(e,data) {
    
        // Пользователь
        var user = $("<div>").appendTo(e).css({
            position:"relative"
        });
        
        $("<img>").attr("src",data.userpick)
            .attr("align","absmiddle")
            .css({
                marginRight:3
            })
            .appendTo(user);
            
        $("<span>").html(data.user)
            .css({
                fontSize:11
            }).appendTo(user);
            
        $("<div>").html(data.time)
            .css({
                position:"absolute",
                right:0,
                top:0,
                fontSize:11
            })
            .appendTo(user);
        
        // Текст
        var textContainer = $("<div>").appendTo(e);
        if(data.type == 5){
            textContainer.css({color: "#FFF", background: "red"});    
        }
        $("<span>").html(data.text+" ").appendTo(textContainer);
        
        if(data.taskText) {
            $("<span>").html(" ("+data.taskText+")")
                .css({
                    opacity:.7,
                    fontStyle:"italic"
                }).appendTo(textContainer);
        }
        
        for(var i in data.files) {
        
            $("<img>")
                .attr("src",data["files"][i].preview)
                .data("path",data["files"][i].path)
                .css({
                    cursor:"pointer",
                    margin:"0px 5px 5px 0"
                }).click(function(e) {
                    window.open($(this).data("path"));
                    e.stopPropagation();
                }).appendTo(e);
        }
    
    },
    
    cmd_handleItemClick:function(id) {
    
        var taskID = this.info("item",id).taskID;
        
        if(!taskID) {
            return;
        }
    
        var task = inx({
            type:"inx.mod.board.task",
            taskID:taskID
        }).cmd("render");
        
    }
         
});