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
            height:100
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
        this.cmd("fetchClass");
    },
    
    cmd_fetchClass:function() {
        this.className = this.classList.pop();
        if(!this.className) {
            this.cmd("done");
        } else {
            this.cmd("log",this.className);
            this.fromID = 0;
            this.cmd("step");
        }
    },
    
    cmd_done:function() {
        this.cmd("log","done");
    },
    
    cmd_log:function(log) {
        inx.msg(log);
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