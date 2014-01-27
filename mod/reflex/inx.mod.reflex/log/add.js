// @link_with_parent

inx.mod.reflex.log.add = inx.panel.extend({

    constructor:function(p) {    
    
        p.style = {
            padding:5,
            spacing:5,
            background:"#ededed"
        }
    
        p.items = [{
            type:"inx.textarea",
            height:"content",
            name:"text"
        },{
            type:"inx.button",
            text:"Написать (Ctrl+Enter)",
            onclick:[this.id(),"send"]
        }];
        this.base(p);
    },
    
    cmd_send:function() {
        var input = this.items().eq("name","text");
        var txt = $.trim(input.info("value"));
        if(!txt)
            return;
        this.fire("send",txt);
        input.cmd("setValue","").cmd("focus");
    }
    
});