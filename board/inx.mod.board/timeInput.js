// @include inx.dialog

inx.ns("inx.mod.board").timeInput = inx.dialog.extend({

    constructor:function(p) {
    
        p.layout = "inx.layout.absolute";
    
        p.style = {
            width:500,
            padding:20,
            background:"white",
            border:0
        };

        p.destroyOnEscape = true;

        p.title = "Сколько времени потрачено?";
               
        this.base(p);
        
        this.sessionHash = (new Date()).getTime();
        
        inx.hotkey("esc",[this.id(),"handleEsc"]);
        this.cmd("requestData");
        
        this.on("render",function() {
            inx(this).items().eq("name","hours").task("focus");
        });
        
    },
    
    cmd_requestData:function() {
        this.call({
            cmd:"board/controller/task/getTaskTime",
            taskID:this.taskID
        },[this.id(),"handleData"]);
    },
    
    cmd_handleData:function(p) {
    
        if(!p) {
            this.task("destroy");
            return;
        }
    
        this.cmd("add",{
            type:"inx.textfield",
            height:30,
            width:50,
            name:"hours",
            value:p.hours
        });
        
        this.cmd("add",{
            type:"inx.textfield",
            height:30,
            width:50,
            x:60,
            name:"minutes",
            value:p.minutes
        });
        
        this.cmd("add",{
            type:"inx.button",
            height:30,
            width:50,
            x:120,
            text:"Сохранить",
            onclick:[this.id(),"save"]
        });
        
        this.cmd("add",{
            type:"inx.textarea",
            name:"comment",
            y:40,
        });
        
        this.on("submit",[this.id(),"save"]);
        
        this.cmd("addSidePanel",{
            type:"inx.mod.board.attachments",
            region:"bottom",
            dropArea:this.el,
            sessionHash:this.sessionHash,
            taskID:this.taskID
        });
        
    },
    
    cmd_save:function() {
    
        var data = this.info("data");
        var h = data.hours*1 + data.minutes/60;
    
        this.call({
            cmd:this.loader,
            taskID:this.taskID,
            sessionHash:this.sessionHash,
            comment:data.comment,
            time:h
        },[this.id(),"handleSave"]);
        
    },
    
    cmd_handleSave:function(ret) {
        if(!ret) {
            return;
        }
        this.fire("save");
        this.task("destroy");
    }
     
});