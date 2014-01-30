// @include inx.dialog

inx.ns("inx.mod.board").tagEditor = inx.dialog.extend({

    constructor:function(p) {    
        p.width = 320;        
        this.base(p); 
        this.on("render","requestData");       
    },
    
    cmd_requestData:function() {
        this.call({
            cmd:"board/controller/tag/getTaskTags",
            taskID:this.taskID
        },[this.id(),"handleData"]);
    },
    
    cmd_handleData:function(data) {
    
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