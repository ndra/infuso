// @include inx.panel

inx.ns("inx.mod.reflex").sync = inx.panel.extend({

    constructor:function(p) {
    
        p.width = 1000;
        
        p.style = {
            border:0,
            spacing:10
        }
        
        p.items = [{
            type:"inx.panel",
            name:"log",
            style:{
                padding:10
            }
        },{
            type:"inx.button",
            text:"Синхронизировать",
            onclick:[this.id(),"start"]
        }]
        
        this.base(p);
    },
    
    cmd_start:function() {
        this.call({
            cmd:"reflex/sync/getClassList"
        },[this.id(),"handleClassList"])
    },
    
    cmd_handleClassList:function(data) {
        this.classList = data;
        
        var container = $("<div>");
        
        for(var i in data) {
            $("<div>")
                .html(data[i])
                .addClass(data[i])
                .appendTo(container);
        }
        
        this.items().eq("name","log").cmd("html",container);
        
        this.cmd("fetchClass");
    },
    
    cmd_fetchClass:function() {
        this.className = this.classList.shift();
        if(!this.className) {
            this.cmd("done");
        } else {
            this.fromID = 0;
            this.cmd("step");
        }
    },
    
    cmd_done:function() {
        this.cmd("log","done");
    },
    
    cmd_log:function(log) {
        var container = this.items().eq("name","log").info("param","__body");
        
        var item = container.find("."+log["class"]);
       
        item.html(log.message);
    },
    
    cmd_step:function() {
        this.call({
            cmd:"reflex/sync/syncStep",
            className:this.className,
            fromID:this.fromID
        },[this.id(),"handleStep"],[this.id(),"stepFailed"])
    },
    
    cmd_stepFailed:function() {
        this.cmd("step");
    },
    
    cmd_handleStep:function(data) {
    
        if(!data) {
            this.cmd("stepFailed");
            return;
        }        
        
        if(data.log) {
            this.cmd("log",data.log)
        }
        
        if(data.action == "nextClass") {
            this.cmd("fetchClass");
            return;
        }
        
        if(data.action == "nextID") {
            this.fromID = data.nextID;
            this.cmd("step");
            return;
        }
        
    }
     
});