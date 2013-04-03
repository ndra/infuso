// @include inx.panel

inx.css("html,body{padding:0px;margin:0px;overflow:hidden;}");

inx.viewport = inx.panel.extend({

    constructor:function(p) {
        if(!p.style)
            p.style = {}
            
        if(p.style.border===undefined)
            p.style.border = 0;
        p.layout = "inx.layout.fit";
        p.autoHeight = false;
        this.base(p);
    },
    
    cmd_render:function(c) {
        this.base(c);
        var id = this.id();
        $(window).resize(function(){
            inx(id).task("syncToWindow");
        });
        this.task("syncToWindow");
    },
    
    cmd_syncToWindow:function() {   
    
        var width = $(this.container).width();
        this.style("width",width);        
    
        var e = $("<div style='position:absolute;width:1px;height:1px;background:red;' ></div>").prependTo("body");
        var top = e.offset().top;
        e.remove();

        var e = $("<div style='position:absolute;width:1px;height:1px;background:red;' ></div>").appendTo("body");
        var bottom = e.offset().top;
        e.remove();

        var ch = $(this.container).height();
        var height = (ch+ $("html").get(0).clientHeight - (bottom-top));
        
        this.style("height",height);
    }
    
});
