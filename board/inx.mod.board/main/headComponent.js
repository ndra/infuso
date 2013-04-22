// @link_with_parent

inx.mod.board.main.headComponent = inx.panel.extend({

    constructor:function(p) {
        p.layout = "inx.layout.column";
        p.style = {
            padding:4,
            background:"#ededed"
        }
        this.base(p);
    },
    
    cmd_update:function(items,selected) {
    
        this.items().cmd("destroy");    
    
        this.cmd("add",{
            type:"inx.textfield",
            name:"search",
            onchange:[this.id(),"fireLoadEvent"],
            width:100
        });
        
        this.cmd("add",{
            type:"inx.pager",
            name:"pager",
            onchange:[this.id(),"fireLoadEvent"]
        });
        
        for(var i=0;i<items.length;i++) {
        
            var cmp = inx(items[i]);
            
            var text = cmp.info("title");
            
            if(cmp.id()==selected) {
                text = "<div style='border-bottom:2px solid red;font-weight:bold;' >"+text+"</div>";
            }
            
            this.cmd("add",{
                type:"inx.button",
                air:true,
                text:text,
                onclick:inx.cmd(this,"fireSelectEvent",items[i])
            });
        }

    },
    
    cmd_extendLoader:function(loader) {
        loader.search = this.items().eq("name","search").info("value");
        loader.page = this.items().eq("name","pager").info("value");
    },
    
    cmd_fireSelectEvent:function(id) {
        this.fire("select",id);
    },
    
    cmd_fireLoadEvent:function(id) {
        this.bubble("updateTaskList",id);
    },
    
    cmd_handleTaskLoad:function(data) {
        this.items().eq("name","pager").cmd("setTotal",data.pages);    
    }
         
});