// @link_with_parent
// @include inx.list

inx.mod.board.task.log = inx.list.extend({

    constructor:function(p) {
        p.background = "#ededed";
        p.loader = {cmd:"board:controller:reportLogForTask",taskID:p.taskID};
        p.bbar = [{
            type:"inx.textarea",
            name:"text",
            autoHeight:true,
            width:300
        },{
            type:"inx.button",text:"Написать",icon:"mail",air:true,onclick:[this.id(),"send"]
        }];
        this.base(p);
    },
    
    cmd_send:function() {
        var textarea = inx(this).axis("bbar").items().eq("name","text");        
        this.call({
            cmd:"board:controller:taskSendMessage",
            taskID:this.taskID,
            text:textarea.info("value")
        },[this.id(),"handleSend"]);
    },
    
    cmd_handleSend:function() {
        var textarea = inx(this).axis("bbar").items().eq("name","text");
        textarea.cmd("setValue","");
        this.task("load");
    }
         
});