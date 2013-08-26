// @include inx.list,inx.pager,inx.dialog
// @link_with_parent

inx.mod.reflex.fields.links.addObject = inx.dialog.extend({

    constructor:function(p) {
    
        p.style = {
            width:320,            
            border:0
        };

        p.title = "Добавление элемента";

        this.list = inx({
            type:"inx.list",
            loader: {
                cmd:"reflex:editor:fieldController:getListItems",
                index:p.index,
                name:p.name
            },
            listeners: {
                itemdblclick:[this.id(),"addObject"]
            }, style: {
                vscroll:true,
                maxHeight:300
            }
        }) 
        
        p.items = [this.list];
        
        this.pager = inx({
            type:"inx.pager",
            onchange:[this.list.id(),"load"]
        });
        
        this.search = inx({
            type:"inx.textfield",
            width:100,
            onchange:[this.list.id(),"load"]
        });
        
        p.tbar = [
            {text:"Добавить",icon:"plus",onclick:[this.id(),"addObject"]},
            "|",
            this.search,
            {icon:"refresh",onclick:[this.list.id(),"load"]},
            this.pager
        ];
        
        this.list.on("load",[this.id(),"handleLoad"]);
        this.list.on("beforeload",[this.id(),"beforeLoad"]);
        
        p.destroyOnEscape = true;
        
        this.base(p);
    },
    
    cmd_handleLoad:function(data) {
        this.pager.cmd("setTotal",data.pages);
    },
    
    cmd_beforeLoad:function(data) {
        data.page = this.pager.info("value");
        data.search = this.search.info("value");
    },
   
    cmd_addObject:function() {
        var id = this.list.info("selection")[0];
        if(!id) return;
        this.fire("addObject",id);
        this.task("destroy");
    }
    
});