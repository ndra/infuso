// @include inx.dialog

inx.ns("inx.mod.board").vote = inx.panel.extend({

    constructor:function(p) {
        
        p.items = [{
            type:"inx.panel",
            layout:"inx.layout.form",
            name:"top",
            style:{
                border:0
            }
        },{
            type:"inx.panel",
            name:"bottom",
            style:{
                border:0
            }
        }]
    
        p.style = {
            width:500,
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
    
        var taskID = this.taskID;
        
        var top = this.items().eq("name","top");
        var bottom = this.items().eq("name","bottom");
    
        for(var i in data) {
            switch(data[i].type) {
            
                default:
                    top.cmd("add",{
                        type:this.info("type")+"."+"criteria",
                        taskID:this.taskID,
                        criteriaID:data[i].id,
                        label:data[i].title,
                        labelAlign:"left",
                        value:data[i].score,
                        style:{
                            border:0
                        }
                    });
                    break;
                
                case 2:
                    bottom.cmd("add",{
                        type:"inx.checkbox",
                        criteriaID:data[i].id,
                        label:data[i].title,
                        value:!!data[i].score,
                        labelAlign:"top",
                        onchange:function() {
                            var val = this.info("value");
                            this.call({
                                cmd:"board/controller/vote/vote",
                                taskID:taskID,
                                criteriaID:this.criteriaID,
                                score:val
                            });
                        }
                    });
                    break;
            
            }
        }
    }
         
});