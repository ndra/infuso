// @link_with_parent

inx.list.item = inx.panel.extend({

    constructor:function(p) {
    
        if(!p.style) {
            p.style = {};
        }
        
        p.style.border = 0;
        p.style.padding = 4;
        
        this.on("dblclick","extendEvent");
        this.on("click","extendEvent");
        
        this.base(p);
    },
    
    
    /**
     * Добавляет в событие мыши информацию о клике
     **/
    cmd_extendEvent:function(e) {
    
        // Действие
        switch(e.type) {
            case "click":
                e.action = this.data.click;
                break;
            case "dblclick":
                e.action = this.data.dblclick;
                break;
        }
        
    },
    
    cmd_render:function() {
    
        this.base();        
        var e = $("<div>");
        var obj = this.list;
        obj.renderer(e,this.data.data);
        this.cmd("html",e);
        
        if(this.data.css) {
            e.css(this.data.css);
        }
        
    }

});