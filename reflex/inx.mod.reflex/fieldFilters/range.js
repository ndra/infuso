inx.ns("inx.mod.reflex.fieldFilters").range = inx.panel.extend({

    constructor:function(p) {
    
        p.layout = "inx.layout.column";
        
        var type;
        switch(p.filterType) {
            case "date":
                type = "inx.date"
                break;
            case "datetime":
                type = "inx.mod.reflex.fieldFilters.range.datetime"
                break;
            default:
                type = "inx.textfield";
                break;
        }
        
        p.items = [
            {type:"inx.panel",width:20,html:"<span style='color:gray'>от</span>",height:22,style:{border:0,padding:4,background:"none"}},
            {type:type,value:"",width:50,name:"from"},
            {type:"inx.panel",width:20,html:"<span style='color:gray'>до</span>",height:22,style:{border:0,padding:4,background:"none"}},
            {type:type,end:true,value:"",width:50,name:"to"}
        ];
        
        p.style = {
            border:0,
            spacing:2,
            height:"content"
        }
        
        p.background = "none";
        this.base(p);
    },
    
    info_value:function() {
        return {
            from:this.items().eq("name","from").info("value"),
            to:this.items().eq("name","to").info("value")
        };
    }
    
})
