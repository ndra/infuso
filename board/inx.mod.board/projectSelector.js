// @include inx.dialog

inx.ns("inx.mod.board").projectSelector = inx.dialog.extend({

    constructor:function(p) {    
    
        p.title = "Выберите проект";
        
        p.style = {
            width:400
        }     
        
        p.tbar = [{
            type:"inx.textfield",
            width:"parent",
            listeners:{
                render:function()
                {
                    this.task("focus");
                }
            }
        }]
        
        this.list = inx({
            type:"inx.list",
            style:{
                maxHeight:400
            },
            loader:{
                cmd:"board/controller/project/listProjects",
                taskID:this.taskID
            }
        });
        
        p.items = [this.list];
        
        p.destroyOnEscape = true;
        
        this.base(p); 
        this.on("render","requestData");       
    }
         
});