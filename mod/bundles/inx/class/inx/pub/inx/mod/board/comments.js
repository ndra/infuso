// @include inx.list,inx.textarea
/*-- /board/inx.mod.board/comments.js --*/


inx.ns("inx.mod.board").comments = inx.list.extend({

    constructor:function(p) {
    
        if(!p.style) {
            p.style = {};
        }
        
        p.style.spacing = 10;
    
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
        inx.hotkey("f5",[this.id(),"handleF5"]);
    },
    
    cmd_handleF5:function() {
        this.cmd("load");
        return;
    },
    
    cmd_handleBeforeLoad:function(loader) {
        loader.mode = inx(this).axis("side").eq("name","controls").info("value");
    },
    
    renderer:function(e,data) {
    
        // Отметка даты
        if(data.date) {
            $("<div>")
                .html(data.date)
                .appendTo(e)
                .css({
                    opacity:.5,
                    fontStyle:"italic",
                    paddingLeft:10
                });
            return;
        }
        
        e.css({
            paddingLeft:35
        })
        
        if(data.timeSpent) {
            $("<div>").html(data.timeSpent)
                .css({
                    position:"absolute",
                    right:0,
                    top:3,
                    fontSize:11,
                    opacity:.5
                })
                .appendTo(e);
        }
        
        $("<div>").html(data.time)
            .css({
                position:"absolute",
                left:0,
                top:3,
                fontSize:11,
                opacity:.5
            })
            .appendTo(e);
    
        // Пользователь
        var user = $("<span>").appendTo(e).css({
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
                marginRight:5,
                fontWeight:"bold"
            }).appendTo(user);
            
        // Текст
        var textContainer = $("<span>").appendTo(e);
        if(data.type == 5) {
            textContainer.css({
                color: "#FFF",
                background: "red"
            });
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

/*-- /board/inx.mod.board/comments/controls.js --*/


inx.mod.board.comments.controls = inx.panel.extend({

    constructor:function(p) {
        if(!p.style) {
            p.style = {};
        }
        p.style.padding = 5;
        p.style.height = 30;
        this.base(p);
    },
    
    cmd_render:function() {
    
        this.base();
        var e = $("<div>");
        var cmp = this;
        
        this.a = $("<span>").html("Важное").css({
            marginRight:10
        }).appendTo(e)
        .click(function() {
            cmp.cmd("setMode",0);
        });
        
        this.b = $("<span>").html("Все").css({
            marginRight:10
        }).appendTo(e)
        .click(function() {
            cmp.cmd("setMode",1);
        });
        
        this.cmd("html",e);
        this.suspendEvents();
        this.cmd("setMode",0);
        this.unsuspendEvents();
    },
    
    cmd_setMode:function(mode) {
        if(mode) {
            this.a.css({fontWeight:"normal"});
            this.b.css({fontWeight:"bold"});
        } else {
            this.a.css({fontWeight:"bold"});
            this.b.css({fontWeight:"normal"});
        }
        this.private_value = mode;
        this.fire("change",mode);
    },
    
    info_value:function() {
        return this.private_value;
    }
         
});

/*-- /board/inx.mod.board/comments/send.js --*/


inx.mod.board.comments.send = inx.textarea.extend({

    constructor:function(p) {
    
        if(!p.style) {
            p.style = {};
        }
        p.style.height = "content";
    
        this.base(p);
    },
    
    cmd_keydown:function(e) {
    
        if(e.which==13 && !e.ctrlKey) {
            this.cmd("save");
            e.preventDefault();
            return "stop";
        }
        
        if(e.which==13 && e.ctrlKey) {
            this.cmd("replaceSelection","\n");
        }
        
        return this.base(e);
    },
    
    cmd_save:function() {
   
        this.call({
            cmd:"board/controller/log/sendMessage",
            taskID:this.taskID,
            text:this.info("value")
        },[this.id(),"handleSave"]);
        
        this.cmd("setValue","");
    
    },
    
    cmd_handleSave:function() {
        this.fire("send");
    }
         
});

