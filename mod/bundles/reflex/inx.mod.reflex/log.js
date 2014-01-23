// @include inx.list

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