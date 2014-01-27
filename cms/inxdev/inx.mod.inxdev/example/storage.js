// @include inx.list

inx.ns("inx.mod.inxdev.example").storage = inx.list.extend({

    constructor:function(p) {
        p.tbar = [
            {id:"key",type:"inx.textfield",width:100,value:"key"},
            {id:"val",type:"inx.textfield",width:100,value:"value"},
            {text:"send",onclick:[this.id(),"test"]}
        ]
        p.autoHeight = true;
        p.maxHeight = 200;
        this.base(p);
        inx.storage.onready(this,"onstorage");
    },
    
    cmd_test:function() {
        var key = inx("key").info("value");
        var val = inx("val").info("value");
        inx.storage.set(key,val);
        this.task("update");
    },
    
    cmd_onstorage:function() {
        this.task("update");
    },
    
    cmd_update:function() {   
    
        var keys = inx.storage.keys();
        
        var data = [];
        for(var i in keys)
            data.push({text:"<b>"+keys[i]+"</b>: "+inx.storage.get(keys[i])});
        this.cmd("setData",data);
    }

})