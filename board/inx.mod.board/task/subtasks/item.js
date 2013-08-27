// @link_with_parent
// @include inx.panel

inx.mod.board.task.subtasks.item = inx.panel.extend({

    constructor:function(p) {    
        p.style = {
            
        }
        
        p.html = p.data.text;
        
        this.base(p);
    },
    
    cmd_render:function() {
        this.base();
        this.el.mouseenter(inx.cmd(this.id(),"showControls")).mouseleave(inx.cmd(this.id(),"hideControls"));
    },
    
    cmd_showControls:function() {
    
        if(!this.controls) {
    
            var cmp = inx({
                width:100,
                tools:this.data.data.tools,
                type:"inx.mod.board.taskControls",
                taskID:this.data.id,
                region:"right"
            });            
            
            this.controls = cmp;
            this.cmd("addSidePanel",this.controls);
        }
        
        this.controls.cmd("show");
    },
    
    cmd_hideControls:function() {
        if(this.controls) {
            this.controls.cmd("hide");
        }
    }
     
});