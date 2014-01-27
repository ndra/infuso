// @include inx.panel

inx.iframe = inx.panel.extend({

    constructor:function(p) {
    
        if(!p.style) {
            p.style = {};
        }
        
        p.style.padding = 0;
        
        this.base(p);
        
        inx.service("events").global([this.id(),"handleEvent"]);
    },
    
    cmd_handleEvent:function(name,p1,p2,p3) {
        var wnd = this.iframe.get(0).contentWindow;
        if(!wnd.inx) {
            return;
        }
        wnd.inx.fire(name,p1,p2,p3);
    },
    
    cmd_render:function() {
    
        this.base();
        
        this.iframe = $("<iframe>")
            .css({
                position:"absolute",
                margin:0, 
                left:0,
                top:0
            });
            
        this.cmd("html",this.iframe);
        this.cmd("setURL",this.src);

    },
    
    /**
     * Изменяет адрес iframe на данный
     **/
    cmd_setURL:function(src) {
    
        if(this.private_lastURL == src) {
            return;
        }
        this.private_lastURL = src;
    
        if(!src) {
            return;
        }
        
        this.src = src;
           
        if(this.iframe) {
            this.iframe.attr("src",src);
        }
        
    },
    
    /**
     * Перезагружает фрейм
     **/
    cmd_refresh:function() {
        var wnd = this.iframe.get(0).contentWindow;
        wnd.location.href = this.src;
    },
    
    cmd_syncLayout:function() {
    
        this.base();
        
        this.iframe.css({
            width:this.info("bodyWidth"),
            height:this.info("bodyHeight"),
        })
    }
    
});