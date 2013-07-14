// @link_with_parent
// @include inx.tabs

inx.mod.reflex.meta.title = inx.tabs.extend({

    constructor:function(p) {
        p.selectNew = false;      
        this.base(p);        
        this.cmd("requestData");
    },
        
    cmd_requestData:function() {
        this.call({
            cmd:"lang:controller:getAll",
        },[this.id(),"handleData"]);
    },
    
    cmd_handleData:function(data) {
    
        var that = this;
        var n = 0;
        for(var i in data) {
            n++;
            that.cmd("add",{
                type:"inx.mod.reflex.meta.title.lang",
                index:that.index,
                lang:i,
                title:data[i],
                lazy:true
            });
        }
        
        if(n < 2) {
            this.cmd("hideTabs");
        }        
        
    }
     
});