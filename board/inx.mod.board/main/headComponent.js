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
    
    cmd_update:function(items) {
    
        this.items().cmd("destroy");    
    
        this.cmd("add",{
            type:"inx.textfield",
            width:100
        });
        
        for(var i=0;i<items.length;i++) {
            var cmp = inx(items[i]);
            this.cmd("add",{
                type:"inx.button",
                air:true,
                text:cmp.info("title")
            });
        }

    }
         
});