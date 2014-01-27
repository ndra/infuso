// @include inx.panel
inx.ns("inx.mod.inxdev.example").caret = inx.panel.extend({

    constructor:function(p) {        
    
        this.textfield = inx({
            type:"inx.textfield",
            value:"123"
        })
        
        this.textarea = inx({
            type:"inx.textarea",
            value:"111\n222"
        })
    
        p.items = [this.textfield,this.textarea];
        
        this.log = inx({
            type:"inx.panel",
            region:"bottom"
        });
        p.side = [this.log];
        
        p.tbar = [{
            text:"Embrace",onclick:[this.id(),"testEmbrace"]
        },{
            text:"Reselect",onclick:[this.id(),"testSelect"]
        }]
    
        this.base(p);
        
        setInterval(inx.cmd(this.id(),"handle"),100);
        
    },
    
    cmd_testEmbrace:function() {
        this.textfield.cmd("replaceSelection","[","]");
        this.textarea.cmd("replaceSelection","[","]");
    },
    
    cmd_testSelect:function() {
        inx.msg("reselect")
        var caret = this.textfield.info("caret");
        this.textfield.cmd("setCaret",caret.start,caret.end);
        var caret = this.textarea.info("caret");
        this.textarea.cmd("setCaret",caret.start,caret.end);
    },

    cmd_handle:function() {
        var caret = this.textfield.info("caret");
        if(!caret)
            return;
        var caret2 = this.textarea.info("caret");
        if(!caret2)
            return;
        
        var html = "";
        html+= caret.start+" - "+caret.end;
        html+= " | ";
        html+= caret2.start+" - "+caret2.end;
        this.log.cmd("html",html);
    }

})