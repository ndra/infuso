// @link_with_parent
// @include inx.dd

inx.css(    
    ".inx-dialog-closeButton{float:right;cursor:pointer;width:20px;height:18px;background:url(%res%/img/components/close.gif) center center no-repeat}",
    ".inx-dialog-title{cursor:move;background:none;}",
    ".inx-dialog-titleText{float:left;padding:3px 0px 0px 5px;font-weight:bold;color:#555555;}"    
);

inx.dialog.title = inx.box.extend({

    constructor:function(p) {
        p.style = {
            border:0
        }
        this.base(p);
    },

    cmd_render:function(c) {
        this.base(c);
        this.el.addClass("inx-dialog-title");
        
        this.titleText = $("<div>").addClass("inx-dialog-titleText").html(this.title+"").appendTo(this.el);
        
        if(this.closeButton)
            this.closeButton = $("<div>")
                .addClass("inx-dialog-closeButton")
                .appendTo(this.el)
                .click(inx.dialog.title.close);
                
        inx.dd.enable(this.el,this,"drag");        
    },
    
    cmd_drag:function(x,y) {
        this.fire("drag",x,y);
    },
    
    cmd_setTitle:function(title) {
        $(this.titleText).html(title);
    }
    
})

inx.dialog.title.close = function(e) {
    inx.cmp.fromElement(e.target).owner().cmd("destroy");    
}
