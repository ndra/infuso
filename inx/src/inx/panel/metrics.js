// @link_with_parent

inx.panel = inx.panel.extend({

    /**
     * Обновляет положение и размеры элемента __body панели
     **/
    cmd_updateBodyBox:function() {
    
        if(!this.__body) {
            return;
        }            
            
        if(this.info("hidden")) {
            return;
        }
    
        var padding = this.style("padding");
        
        var width = inx.geq(this.__bodyWidth - padding*2,1);
        
        this.__body.css({
            width:width,
            left:padding,
            top:padding
        });
            
        this.private_bodyContainer.css({
            left:this.private_bodyContainerLeft,
            top:this.private_bodyContainerTop,
            width:inx.geq(this.__bodyWidth,1),
            height:inx.geq(this.__bodyHeight,1)
        }); 
        
    },
    
    info_bodyHeight:function() {
        return this.__bodyHeight || 0;
    },

    info_bodyWidth:function() {
        return this.__bodyWidth || 0;
    },
    
    /**
     * Возвращает ширину доступной области
     * Доступная область - та в которой можно вывести информацию
     * Это внутренний размер, без паддинга, скроллбара и т.п.
     **/
    info_clientWidth:function() {
        return Math.max(0,(this.__bodyWidth || 0) - this.style("padding")*2 - (this.private_style.vscroll ? 10 : 0));
    },
    
    info_clientHeight:function() {
        return Math.max(0,(this.__bodyHeight || 0) - this.style("padding")*2 - (this.private_style.hscroll ? 10 : 0));
    },
    
});
