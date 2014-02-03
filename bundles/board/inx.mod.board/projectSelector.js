// @include inx.dialog

inx.ns("inx.mod.board").projectSelector = inx.dialog.extend({

    constructor:function(p) {    
    
        p.title = "Выберите проект";
        
        p.style = {
            width:400
        }     
        
        this.list = inx({
            type:"inx.list",
            style:{
                maxHeight:400
            },
            loader:{
                cmd:"infuso/board/controller/project/listProjectsSimple",
                taskID:this.taskID
            },
            listeners:{
                beforeload:function(loader) {
                    loader.search = this.owner().axis("tbar").items().eq("name","search").info("value");
                }, itemclick:function(id) {
                    this.owner().fire("select",id);
                    this.owner().task("destroy");
                }
            }
        });
        
        p.tbar = [{
            type:"inx.textfield",
            width:"parent",
            name:"search",
            listeners:{
                render:function() {
                    this.task("focus");
                },change:[this.list.id(),"load"]
            }
        }]
            
        p.items = [this.list];
        
        p.destroyOnEscape = true;
        
        this.base(p); 
        this.on("render","requestData");       
    }
         
});