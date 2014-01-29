// @include inx.button,inx.viewport

inx.ns("inx.mod.reflex").map = inx.viewport.extend({

    constructor:function(p) {
        p.height = 400;
        p.title = "Обслуживание каталога";
        p.autoHeight = false;
        this.className = inx({type:"inx.textfield",width:150});
        p.tbar = [this.className,"|",{
            icon:"refresh",
            text:"Начать",
            onclick:[this.id(),"start"]
        }];
        this.status = inx({
            type:"inx.panel",
            autoHeight:true,
            padding:10,
            background:"#ededed",
            region:"bottom"
        });
        p.side = [this.status]
        if(p.oncomplete) this.on("complete",p.oncomplete);
        this.base(p);
    },
    
    cmd_start:function() {
        this.step = "";
        this.processed = 0;
        this.cmd("html","");
        this.cmd("step");
    },
    
    cmd_step:function() {
        this.call({cmd:"reflex:map:step",step:this.step,className:this.className.info("value")},[this.id(),"handleStep"]);
    },
    
    cmd_handleStep:function(step,meta) {
    
        if(meta) {
            if(meta.status) this.cmd("log",meta.status);
            if(meta.total) this.total = meta.total;
            if(meta.processed) this.processed+= meta.processed;
        }
    
        var s = "";
        var percent = Math.round((this.processed / this.total)*10000)/100;
        s+= this.processed+"/"+this.total+" ("+percent+"%)";
        this.status.cmd("html",s);
        
        if(step) {
            this.step = step;
            this.cmd("step");
        } else {
            this.fire("complete");
        }
        
        this.task("resizeToContents");
    },
    
    cmd_log:function(html) {
        var e = $("<div>").html(html+"");
        e.appendTo(this.__body);
    }
     
});