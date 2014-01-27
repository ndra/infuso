// @include inx.panel,inx.date,inx.layout.column

inx.ns("inx.mod.seo").date = inx.panel.extend({

    constructor:function(p) {
        p.layout = "inx.layout.column";
        p.padding = 5;
        p.background = "#ededed";
        p.items = [
            {type:"inx.date",name:"date"},
            {type:"inx.button",text:"Показать",onclick:[this.id(),"setDate"]}
        ]
        this.base(p);
    },
    
    cmd_setDate:function() {
        var date = this.items().eq("name","date").info("value");
        window.location.href = "?date="+date;
    }

});