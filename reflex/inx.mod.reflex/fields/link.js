// @include inx.combo

inx.ns("inx.mod.reflex.fields").link = inx.panel.extend({

    constructor:function(p) {
        p.layout = "inx.layout.column";
        p.labelAlign = "left";
        p.width = 300;
        
        p.style = {
            border:0,
            background:"none",
            spacing:2
        }
        
        this.combo = inx({
            type:"inx.combo",
            width:274,
            name:"combo",
            value:p.value,
            text:p.text,
            loader:{cmd:"reflex:editor:fieldController:getListItems",index:p.index,name:p.name}
        });
        
        p.items = [this.combo,{
            type:"inx.button",
            air:true,
            icon:"right",
            onclick:[this.id(),"viewElement"]
        }]
        this.base(p);
    },

    info_value:function() {
        var ret = this.items().eq("name","combo").info("value");
        return ret;
    },

    cmd_viewElement:function() {   
       
        this.call({
            cmd:"reflex_editor_fieldController:listEditURL",
            itemID:this.info("value"),
            name:this.name,
            index:this.index
        },function(url) {
            window.location.href = url;
        });
    
    }

})
