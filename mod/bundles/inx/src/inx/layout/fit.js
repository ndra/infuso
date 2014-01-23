// @link_with_parent

inx.layout.fit = {

    create:function() {},
    
    add:function(cmp) {
    
        if(cmp.data("ij89238v67"))
            return;
    
        cmp = inx(cmp);
        
        var e = $("<div>").css({
            position:"absolute"
        }).appendTo(this.__body);
        
        if(!this.keepBorder)
            cmp.cmd("border",0);
            
        cmp.cmd("render");
        cmp.cmd("appendTo",e);
        cmp.data("layoutContainer",e);
        
        if(this.style("height")=="parent")
            cmp.style("height","parent");
        
    },
    
    remove:function(cmp) {
        $(cmp.data("layoutContainer")).detach();
        cmp.data("ij89238v67",false);
    },
    
    sync:function() {  
    
        var autoHeight = this.style("height")=="content";
        var width = this.info("clientWidth");
        var height = this.info("clientHeight");
        var contentHeight = 0;
        
        if(width<=0)
            return;
        
        this.items().each(function() {
        
            var e = this.data("layoutContainer");
            if(!e)
                return;
        
            if(this.info("visible")) {
                this.cmd("width",width);
                
                if(!autoHeight)
                    this.cmd("height",height);   
                    
                e.css({
                    left:0,
                    top:0,
                    display:"block"
                })
                
                contentHeight = this.info("height");
                    
            } else {
                e.css("display","none");
            }
        });
        
        this.cmd("setContentHeight",contentHeight);     
        
    }
}
