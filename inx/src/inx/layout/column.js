// @link_with_parent

inx.layout.column = {

    add:function(id) {    
        
        var cmp = inx(id);   
        if(cmp.data("d4ubdy9sopvh7")) {
            return;
        }
        
        // Создаем элемент для компонента
            
        cmp = inx(cmp);
        var c = $("<div>").css({
            position:"absolute"
        }).appendTo(this.__body);
        
        cmp.cmd("render");
        cmp.cmd("appendTo",c)
        cmp.data("layoutContainer",c);
        
        cmp.data("d4ubdy9sopvh7",true);
    },    
    
    create:function() {        
    },
    
    sync:function() {
    
        var x = 0;
        var baseline = 0;
        var line = [];
        var xspacing = this.style("spacing");
        var yspacing = xspacing;
        var that = this;
        
        this.items().each(function() {
            this.data("xij89238v67-height",this.info("height"));
        }); 
        
        var completeLine = function() {
        
            if(!line.length) {
                return;
            }
        
            // Определяем высоту линии элементов
            var height = 0;
            for(var i in line) {
                height = Math.max(height,line[i].data("xij89238v67-height"));
            }

            // Центруем элементы по вертикали
            for(var i in line) {
                var e = line[i].data("layoutContainer");
                var top = baseline;
                
                if(that.style("valign")=="center") {
                    top-= line[i].data("xij89238v67-height")/2;
                    top+= height/2;
                }
                
                e.css({
                    top:top
                })
            } 
            
            baseline+= height+yspacing;
            line = [];  
            x = 0;
            
        }
        
        var clientWidth = this.info("clientWidth");
        
        if(clientWidth<1) {
            return;
        }
        
        this.items().cmd("width",clientWidth);
        
        this.items().each(function() {    
        
            if(!this.info("layoutReady")) {
                return;
            }
        
            if(this.style("break")) {
                completeLine();
            }
        
            e = this.data("layoutContainer");
            
            if(this.info("visible")) {
            
                var width = this.info("width");
                
                if(x + width > clientWidth) {
                    completeLine();
                }
                
                e.css({
                    left:x,
                    display:"block"
                });
                
                line.push(this);
                
                x += this.info("width");
                x+= xspacing;
            
            } else {
                e.css({
                    display:"none"
                })
            }
                
        });
        
        completeLine();
        this.cmd("setContentHeight",baseline - yspacing);
        
    },  
     
    remove:function(cmp) {
        $(cmp.data("layoutContainer")).detach();
        cmp.data("d4ubdy9sopvh7",false);
    }
}
