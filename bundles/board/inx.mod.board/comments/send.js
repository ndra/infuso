// @link_with_parent
// @include inx.textarea

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