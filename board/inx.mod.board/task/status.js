// @link_with_parent
// @include inx.panel

inx.mod.board.task.status = inx.panel.extend({

    constructor:function(p) {   
        this.base(p);  
        this.cmd("updateText",p.statusText);
        inx.on("board/taskChanged",[this.id(),"handleTaskChanged"]);      
    },
    
    cmd_handleTaskChanged:function(p) {
    
        if(p.taskID!=this.taskID) {
            return;
        }
        
        if(p.statusText) {
            this.cmd("updateText",p.statusText);        
        }
        
    },
    
    cmd_updateText:function(statusText) {
        this.cmd("html", "<div style='font-size:32px;padding-top:18px;' >" + statusText + "</div>");
    }
     
});