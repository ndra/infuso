// @include inx.list

inx.ns("inx.mod.board").messages = inx.list.extend({

    constructor:function(p) {    
    
        p.loader = {
            cmd:"board/controller/messages/list"
        }
        this.base(p); 
        
        this.on("itemclick",[this.id(),"handleItemClick"]);
        inx.hotkey("f5",[this.id(),"handleF5"]);
        this.on("show",[this.id(),"load"]);
    },
    
    cmd_handleF5:function() {
        this.cmd("load");
        return false;
    },
    
    renderer:function(e,data) {
    
        if(data.date) {
        
            e.html(data.date).css({
                opacity:.5,
                fontStyle:"italic",
                padding:"10px 0 10px 20px"
            });
        
        } else {
        
            e.css({
                paddingLeft:40,
                position:"relative"
            });
        
            var text = $("<div>").html(data.text).appendTo(e);
            
            var time = $("<div>")
                .html(data.time)
                .css({
                    position:"absolute",
                    left:0,
                    top:0,
                    opacity:.5
                }).appendTo(e);
        }
    
    },
    
    cmd_handleItemClick:function(id) {
    }
         
});