// @link_with_parent

inx.panel.separator = inx.box.extend({

    constructor:function(p) {
        p.width = 9;
        p.height = 22;
        p.style = {
            border:0,
            background:"none"
        }
        this.base(p);
    },
    
    cmd_render:function(c) {
        this.base(c);
        $("<div/>").css({
            borderLeft:"1px solid gray",
            height:"100%",
            margin:"0px 4px 0px 4px"
        }).appendTo(this.el);
    }

});