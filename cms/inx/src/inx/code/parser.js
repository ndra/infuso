// @link_with_parent

inx.code.parser = inx.observable.extend({

    constructor:function(p) {
        this.line = 0;
        this.base(p);
        this.lineParser = inx({type:"inx.code.lineParser",lang:this.lang});
        this.stack = [];
        this.energy = 0;
        this.start();
    },
    
    cmd_destroy:function() {
        this.lineParser.cmd("destroy");
        this.base();
    },
    
    cmd_process:function() {
    
        this.energy++;
        if(this.energy<0)
            return;
    
        for(var k=0;k<25;k++) {
        
            var code = this.editor.info("line",this.line);
            
            if(code===undefined) {
                return; 
            }
            
            var visible = this.editor.info("visibleLines");
            
            if(this.line>visible.bottom)
                this.stop();
                
            var ret = this.lineParser.info("parse",code,this.stack[this.line-1]);
            
            this.editor.cmd("updateLineStyle",this.line,ret.style);
            this.stack[this.line] = ret.stack;
            this.line++;
            
        }
      
    },
    
    cmd_lineChanged:function(line) {        
        if(line<this.line)
            this.line = line;
        this.start();
        this.energy = -20;
    },
    
    cmd_scrollChanged:function() {
        this.start();
        this.energy = -10;        
    },
    
    start:function() {
        if(!this.interval)
            this.interval = setInterval(inx.cmd(this,"process"),10);
    },
    
    stop:function() {
        if(this.interval)
            clearInterval(this.interval);
        this.interval = 0;
    }

});
