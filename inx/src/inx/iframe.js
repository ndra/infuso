// @include inx.panel

inx.iframe = inx.panel.extend({

    constructor:function(p) {
        if(!p.style)
            p.style = {};
        p.style.padding = 0;
        this.base(p);
    },
    
    cmd_render:function() {
        this.base();
        this.cmd("setURL",this.url);
    },
    
    cmd_setURL:function(src) {
        
        if(this.iframe) {
            this.iframe.remove();
        }
        
        this.iframe = $("<iframe>")
            .attr("src",src)
            .appendTo(this.__body)
            .css({
                position:"absolute",
                margin:0,
                left:0,
                top:0
            });  
            
        inx.msg(this.iframe.attr("src"),1);
            
       // this.task("syncLayout");
        
    },
    
    /**
     * Перезагружает фрейм
     **/
    cmd_refresh:function() {
        this.iframe.get(0).contentWindow.location.reload();
    },
    
    cmd_syncLayout:function() {
    
        this.base();
        this.iframe.css({
            width:this.info("bodyWidth"),
            height:this.info("bodyHeight"),
        })
    }
    
});