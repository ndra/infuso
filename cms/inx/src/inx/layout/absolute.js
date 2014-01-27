// @link_with_parent

inx.layout.absolute = {

    create:function() {
    },
    
    add:function(cmp) {
    
        if(cmp.data("ij89238v67"))
            return;
    
        cmp = inx(cmp);
        var e = $("<div>").css({
            position:"absolute"
        }).appendTo(this.__body);
        cmp.cmd("render");
        cmp.cmd("appendTo",e)
        cmp.data("layoutContainer",e);
        
        cmp.data("ij89238v67",true);
    },
    
    remove:function(cmp) {
        $(cmp.data("layoutContainer")).detach();
        cmp.data("ij89238v67",false);
    },
    
    sync:function() {    
    
        var width = this.info("clientWidth");
    
        var y = 0;
        this.items().each(function() {
        
            var e = this.data("layoutContainer");
            var top = this.info("param","y") || 0;
            y = Math.max(y,top+this.info("height"));
            
            e.css({
                left:this.info("param","x"),
                top:top
            });
            
            this.cmd("width",width);
            
        });
        
        
        
        this.cmd("setContentHeight",y);
    }
}
