// @link_with_parent

inx.css(".f1bqm1 {position:absolute; color:black; opacity:.7; font-style:italic }");

inx.layout.form = {

    create:function() {},
    
    add:function(cmp) {  
    
        if(cmp.data("ij89238v67"))
            return;       
      
        cmp = inx(cmp);        
        var label = $("<div>").appendTo(this.__body).addClass("f1bqm1").text(cmp.info("param","label") || "");
        var c = $("<div>").css({position:"absolute"}).appendTo(this.__body);
        cmp.cmd("render");
        cmp.cmd("appendTo",c);
        cmp.data("label",label);
        cmp.data("container",c); 
        
        cmp.data("ij89238v67",true);      
    },

    remove:function(cmp) {
        $(cmp.data("container")).detach();
        $(cmp.data("label")).detach();
        cmp.data("ij89238v67",false);
    },
    
    sync:function() {   
    
        var bodyWidth = this.info("clientWidth");
        var p = 0;
        var y = 0;
        var that = this;
        var spacing = this.style("spacing");
        
        this.items().each(function(n) {
        
            var item = this;
            var container = item.data("container");
            var label = item.data("label");
        
            if(this.info("visible")) {
            
                container.css("display","block");
                label.css("display","block");
        
                label.html(item.info("param","label") || "");
                
                var help = item.info("help");
                if(help)
                    $("<span>").html("?").css({
                        marginLeft:10,
                        borderBottom:"1px solid gray",
                        cursor:"pointer"
                    }).attr("title",help)
                    .appendTo(label);
                
                // Расчитываем ширину лэйбла
                var lw = item.info("param","labelWidth");
                if(lw===undefined)
                    lw = that.labelWidth;
                if(lw===undefined)
                    lw = 150;
                switch(item.info("param","labelAlign")) {
                
                    case "left":
                    
                        var tmplw = Math.max(lw-15,0);
                        label.css({
                            left:0,
                            top:y,
                            width:tmplw
                        });
                        container.css({
                            left:lw,
                            top:y,
                            width:bodyWidth-lw
                        });
                        y+= Math.max(label.height(),item.info("height"));
                        this.cmd("width",bodyWidth-lw)
                        break;
                        
                    default:
                    case "top":
                    
                        label.css({
                            left:p,
                            top:y+p,
                            width:bodyWidth
                        });
                        var lh = inx.height(label);
                        
                        if(lh)
                            y+= lh + 4;
                            
                        container.css({
                            top:y,
                        });
                        
                        item.cmd("width",bodyWidth);
                        y+= item.info("height");
                        
                        break;
                }            
                y += spacing;
                
            } else {
                container.css("display","none");
                label.css("display","none");
            }
            
        });
        
        this.cmd("setContentHeight",y-spacing); 

    }
}
