// @link_with_parent
// @include inx.dialog,inx.textfield

inx.code.search = inx.dialog.extend({

    constructor:function(p) {
        p.width = 300;
        this.input = inx({
            type:"inx.textfield",
            listeners:{blur:[this.id(),"close"]}
        });
        p.items = [this.input];
        p.title = "Поиск";
        this.base(p);
        
        var i = this.input;
        setTimeout(function(){
            i.cmd("focus").cmd("select");
        },100);
        this.on("submit","handleSubmit");
        inx.storage.onready(this.id(),"onStorageReady");
    },
    
    cmd_onStorageReady:function() {
        var val = inx.storage.get("viomerdyg2oklbjcus3m")+"";
        this.input.cmd("setValue",val);
    },
    
    cmd_close:function() {
        this.task("destroy");
    },
    
    cmd_handleSubmit:function(e) {
        var str = this.input.info("value");
        this.fire("search",str);
        inx.storage.set("viomerdyg2oklbjcus3m",str+"");
        this.task("destroy");
    }

})
