// @include inx.list

inx.ns("inx.mod.board").access = inx.list.extend({

    constructor:function(p) {    
    
        p.tbar = [{
            icon:"plus",
            text:"Добавить",
            air:true,
            onclick:[this.id(),"newProject"]
        },"|",{
            icon:"delete",
            air:true,
            onclick:[this.id(),"deleteProject"]
        }]
    
        p.loader = {
            cmd:"board/controller/access/accessList"
        }
        this.base(p); 
        
        this.on("itemclick",[this.id(),"editAccess"]);
        inx.hotkey("f5",[this.id(),"handleF5"]);
    },
    
    cmd_handleF5:function() {
        this.cmd("load");
        return false;
    },
    
    cmd_editAccess:function(id) {
        inx({
            type:this.info("type")+".editor",
            accessID:id,
            listeners:{
                destroy:[this.id(),"load"]
            }
        }).cmd("render");
    }
         
});