// @include inx.list
/*-- /reflex/inx.mod.reflex/log.js --*/


inx.ns("inx.mod.reflex").log = inx.list.extend({

    constructor:function(p) {    
        p.loader = {cmd:"reflex:editor:controller:log",index:p.index};
        
        this.add = inx({
            type:"inx.mod.reflex.log.add",
            region:"bottom",
            listeners:{send:[this.id(),"send"]}
        });
        
        p.side = [this.add];
        
        this.base(p);
        this.on("show","handleShow");
    },
    
    cmd_send:function(txt) {
        this.call({
            cmd:"reflex:editor:controller:comment",
            txt:txt,
            index:this.index
        },[this.id(),"load"]);
    },
    
    cmd_handleShow:function() {
        this.task("load");
    }
     
});

/*-- /reflex/inx.mod.reflex/log/add.js --*/


inx.mod.reflex.log.add = inx.panel.extend({

    constructor:function(p) {    
    
        p.style = {
            padding:5,
            spacing:5,
            background:"#ededed"
        }
    
        p.items = [{
            type:"inx.textarea",
            height:"content",
            name:"text"
        },{
            type:"inx.button",
            text:"Написать (Ctrl+Enter)",
            onclick:[this.id(),"send"]
        }];
        this.base(p);
    },
    
    cmd_send:function() {
        var input = this.items().eq("name","text");
        var txt = $.trim(input.info("value"));
        if(!txt)
            return;
        this.fire("send",txt);
        input.cmd("setValue","").cmd("focus");
    }
    
});

