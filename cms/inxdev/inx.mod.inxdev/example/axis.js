// @include inx.panel

inx.ns("inx.mod.inxdev.example").axis = inx.panel.extend({

    constructor:function(p) {
        
        p.tbar = [
            {text:"count",onclick:[this.id(),"count"]},
            {text:"resize",onclick:[this.id(),"resize"]},
            {text:"eq",onclick:[this.id(),"eq"]},
        ];
        
        p.height = 300;
        p.items = [
            {html:1,name:"a",autoHeight:true},
            {html:2,name:"b",autoHeight:true},
            {html:3,name:"a",autoHeight:true},
            {html:4,name:"b",autoHeight:true},
            {html:5,name:"c",autoHeight:true}
        ]
        this.base(p);
    },
    
    cmd_count:function() {
        var c = inx(this).items();
        inx.msg(c.length());
    },
    
    cmd_resize:function() {
        var w = Math.random()*1000+10;
        inx(this).items().cmd("width",w);
    },
    
    cmd_eq:function() {
        inx(this).items().eq("name","a").cmd("destroy");
    }
    

});