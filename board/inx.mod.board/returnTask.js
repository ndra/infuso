// @include inx.dialog

inx.ns("inx.mod.board").returnTask = inx.dialog.extend({

    constructor:function(p) {
    
        p.layout = "inx.layout.form";
    
        p.style = {
            width:500,
            padding:20,
            background:"white",
            spacing: 10,
            border:0
        };
        
        
        
        p.destroyOnEscape = true;

        p.title = "Сколько времени потрачено?";
        
        p.items = [{
            type:"inx.textarea",
            name:"comment",
            label: "Комментарий",
            labelAlign:"top",
            style:{
                width: "parent"
            },
        },{
            type:"inx.mod.board.vote",
            taskID:this.taskID,
            width:"parent"    
        },{
            type:"inx.button",
            text:"Вернуть",
            onclick:[this.id(),"save"]
        }];
        
        this.on("render",function() {
            this.items().eq("name","comment").cmd("focus");
        });
        
        this.on("submit",[this.id(),"save"]);
        
        this.base(p);

        inx.hotkey("esc",[this.id(),"handleEsc"]);
        
        
        
        
    },
    
    cmd_save:function() {
    
        var data = this.info("data");
        this.call({
            cmd:"board/controller/task/changeTaskStatus",
            taskID:this.taskID,
            status:this.status,
            comment:data.comment
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