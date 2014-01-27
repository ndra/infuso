// @link_with_parent
// @include inx.dd

inx.css(    
    ".inx-dialog-closeButton{position:absolute;top:3px;right:3px;cursor:pointer;width:20px;height:18px;background:url(%res%/img/components/close.gif) center center no-repeat}",
    ".inx-dialog-title{cursor:move;positionL:relative;}",
    ".inx-dialog-titleText{padding:6px;font-weight:bold;color:#555555;}"    
);

inx.dialog.title = inx.panel.extend({

    constructor:function(p) {
        this.base(p);
        this.style("background","none");
    },

    cmd_render:function(c) {
        this.base(c);
        
        var el = $("<div>").addClass("inx-dialog-title");
        
        this.titleText = $("<div>")
            .addClass("inx-dialog-titleText")
            .html(this.title+"")
            .appendTo(el);
        
        if(this.closeButton) {
            this.closeButton = $("<div>")
                .addClass("inx-dialog-closeButton")
                .appendTo(el)
                .click(inx.cmd(this,"close"));
        }
                
        inx.dd.enable(el,this,"drag");    
        this.cmd("html",el);
    },
    
    cmd_drag:function(x,y) {
        this.fire("drag",x,y);
    },
    
    cmd_setTitle:function(title) {
        $(this.titleText).html(title);
    },
    
    cmd_close:function() {
        this.owner().cmd("destroy");
    }
    
})