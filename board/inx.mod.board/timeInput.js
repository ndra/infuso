// @include inx.dialog

inx.ns("inx.mod.board").timeInput = inx.dialog.extend({

    constructor:function(p) {
    
        p.layout = "inx.layout.absolute";
    
        p.style = {
            width:320,
            height:100,
            background:"none",
            padding:20,
            border:0
        };

        p.destroyOnEscape = true;

        p.title = "Сколько времени потрачено?";        
        this.base(p);
        
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
        
        this.on("submit",[this.id(),"save"]);
        
    },
    
    cmd_save:function() {
    
        var data = this.info("data");
        var h = data.hours*1 + data.minutes/60;
    
        this.call({
            cmd:"board/controller/task/changeTaskStatus",
            taskID:this.taskID,
            status:this.taskStatus,
            time:h
        },[this.id(),"handleSave"])
    },
    
    cmd_handleSave:function(ret) {
        if(!ret) {
            return;
        }
        this.fire("save");
        this.task("destroy");
    }
     
});