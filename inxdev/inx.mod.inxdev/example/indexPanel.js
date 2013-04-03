// @include inx.panel.indexPanel

inx.ns("inx.mod.inxdev.example").indexPanel = inx.panel.indexPanel.extend({

    constructor:function(p) {
        p.items = [
            {type:"inx.panel",height:200,title:"Заголовок 1"},
            {type:"inx.panel",height:200,title:"Заголовок 2"},
            {type:"inx.panel",height:200,title:"Заголовок 3"},
            {type:"inx.panel",height:200,title:"Заголовок 4"},
        ];
        p.side = [
            {width:50,region:"left",resizable:true},
            {height:50,region:"top",resizable:true}
        ]
        this.base(p);
    }

});

