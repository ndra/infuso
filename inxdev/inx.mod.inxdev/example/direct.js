// @include inx.list,inx.direct

inx.ns("inx.mod.inxdev.example").direct = inx.panel.extend({

    constructor:function(p) {
        this.list1 = inx({
            type:"inx.list", 
            data:[
                {id:1,text:"link 1"},
                {id:2,text:"link 2"},
                {id:3,text:"link 3"}
            ],
            onclick:[this.id(),"handleChange"],
            autoHeight:true
        });
        this.list2 = inx({
            type:"inx.list", 
            data:[
                {id:1,text:"link 4"},
                {id:2,text:"link 5"},
                {id:3,text:"link 6"}
            ],
            onclick:[this.id(),"handleChange"],
            autoHeight:true
        });
        p.items = [this.list1,this.list2];
        p.autoHeight = true;
        p.width = 200;
        this.base(p);
        inx.direct.bind(this,"handleDirect");
    },
    
    cmd_handleChange:function() {
        inx.direct.set(this.list1.info("selection")[0],this.list2.info("selection")[0]);        
    },
    
    cmd_handleDirect:function(p1,p2) {
        this.list1.cmd("select",p1);
        this.list2.cmd("select",p2);
    }

});