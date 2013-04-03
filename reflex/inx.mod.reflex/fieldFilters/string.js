inx.ns("inx.mod.reflex.fieldFilters").string = inx.panel.extend({

    constructor:function(p) {
    
        p.style = {
            border:0,
            background:"none"
        }
    
        p.layout = "inx.layout.column";
        var type = p.date ? "inx.date" : "inx.textfield";
        
        var opts = [{
            id:0,
            text:"не важно"
        },{
            id:"yes",
            text:"да"
        },{
            id:"no",
            text:"Нет"
        }];
        
        if(!p.checkbox) {
            opts.push({id:"=",text:"Равно"});
            opts.push({id:"q",text:"Содержит"});
        }
        
        this.select = inx({
            name:"op",
           // xx:"yy",
            type:"inx.select",
            data:opts,
            width:90,
            value:0,
            name:"op",
            onchange:[this.id(),"updateView"]
        });
        
        p.items = [
            this.select
        ];
        
        if(!p.checkbox) {
            this.query = inx({
                type:"inx.textfield",
                value:"",
                width:100,
                name:"q",
                hidden:true
            });
            p.items.push(this.query);
        }
        
        p.width = 200;
        
        this.base(p);
    },
    
    cmd_updateView:function() {
        var vis = this.select.info("value")=="q" || this.select.info("value")=="=";
        inx(this.query).cmd(vis ? "show" : "hide");
    },
    
    info_value:function() {
        return {
            op:this.items().eq("name","op").info("value"),
            q:this.items().eq("name","q").info("value")
        };
    }
    
})
