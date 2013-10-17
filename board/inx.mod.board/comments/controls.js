// @link_with_parent

inx.mod.board.comments.controls = inx.panel.extend({

    constructor:function(p) {
        if(!p.style) {
            p.style = {};
        }
        p.style.padding = 5;
        p.style.height = 30;
        this.base(p);
    },
    
    cmd_render:function() {
    
        this.base();
        var e = $("<div>");
        var cmp = this;
        
        this.a = $("<span>").html("Важное").css({
            marginRight:10
        }).appendTo(e)
        .click(function() {
            cmp.cmd("setMode",0);
        });
        
        this.b = $("<span>").html("Все").css({
            marginRight:10
        }).appendTo(e)
        .click(function() {
            cmp.cmd("setMode",1);
        });
        
        this.cmd("html",e);
        this.suspendEvents();
        this.cmd("setMode",0);
        this.unsuspendEvents();
    },
    
    cmd_setMode:function(mode) {
        if(mode) {
            this.a.css({fontWeight:"normal"});
            this.b.css({fontWeight:"bold"});
        } else {
            this.a.css({fontWeight:"bold"});
            this.b.css({fontWeight:"normal"});
        }
        this.private_value = mode;
        this.fire("change",mode);
    },
    
    info_value:function() {
        return this.private_value;
    }
         
});