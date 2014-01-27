// @link_with_parent
// @include inx.panel

inx.mod.board.task.tags = inx.panel.extend({

    constructor:function(p) {
    
        p.layout = "inx.layout.column";
    
        p.style = {
            background:"none",
            spacing:5,
            padding:5
        };
       
        this.base(p);        
        this.cmd("requestData");
    },
    
    cmd_requestData:function() {
    
        this.call({
            cmd:"board_controller_tag/getTaskTags",
            taskID:this.taskID
        },[this.id(),"handleData"]);
    
    },
    
    cmd_handleData:function(data) {
    
        this.cmd("add",{
            type:"inx.panel",
            width:16,
            html:"<img src='/board/res/img/icons16/tag.png' />",
        });
        
        for(var i=0;i<data.tags.length;i++) {
        
            this.cmd("add",{
                type:"inx.checkbox",
                label:data.tags[i].tagTitle,
                tagID:data.tags[i].tagID,
                taskID:this.taskID,
                value:data.tags[i].value,
                onchange:function() {
                    this.call({
                        cmd:"board_controller_tag/updateTag",
                        taskID:this.taskID,
                        tagID:this.tagID,
                        value:this.info("value")
                    });
                }
            });
        }
    
    }
     
});