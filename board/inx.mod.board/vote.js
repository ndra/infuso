// @include inx.dialog

inx.ns("inx.mod.board").vote = inx.dialog.extend({

    constructor:function(p) {
    
        p.title = "Оцените задачу";
        p.layout = "inx.layout.form";
    
        p.style = {
            width:500,
            padding:20,
            spacing:20,
            border:0,
            background:"white"
        }

        this.on("render","requestData");

        this.base(p);        
    },
    
    cmd_requestData:function() {
        this.call({
            cmd:"board/controller/vote/getCriterias",
            taskID:this.taskID
        },[this.id(),"handleData"]);
    },
    
    cmd_handleData:function(data) {
        for(var i in data) {
            this.cmd("add",{
                type:this.info("type")+"."+"criteria",
                taskID:this.taskID,
                criteriaID:data[i].id,
                label:data[i].title,
                labelAlign:"left",
                value:data[i].score,
                style:{
                    border:0
                }
            })
        }
    }
         
});