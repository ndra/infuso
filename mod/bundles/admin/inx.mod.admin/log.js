// @include inx.dialog,inx.list

inx.ns("inx.mod.admin").log = inx.dialog.extend({

    constructor:function(p) {
        p.title = "Лог";
        p.width = 800;
        p.autoHeight = true;
        
        if(!p.style)
        p.style = {};

        var data = [];
        if(inx.msg.log)
        for(var i in inx.msg.log)
            data.push({text:inx.msg.log[i].text});

        this.list = inx({
            type:"inx.list",data:data,
            vscroll:true,
            autoHeight:true,
            style:{
                border:0,
                padding:10,
                vscroll:true,
                maxHeight:300
            }
        });
        p.items = [this.list];
        p.autoDestroy = true;
        this.base(p);
    }
        
});