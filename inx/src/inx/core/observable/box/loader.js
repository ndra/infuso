// @link_with_parent

inx.box.loader = inx.box.extend({

    constructor:function(p) {
        this.initialParams = p;
        p.bypassAutoheight = true;
        this.base(p);
        inx.loader.load(p.type,this.id());
        this.private_cmdBuffer = [];
    },

    cmd_render:function() {
        this.base();
        this.el.html("<table style='width:100%;height:100%;'><tr><td style='text-align:center;'>"+inx.conf.componentLoadingHTML+"</td></tr></table>");
    },
    
    info_initialParams:function() {
        var p = this.initialParams;
        p.style = this.private_style;
        p.hidden = this.private_hidden;
        
        p.private_widthContent =  this.private_widthContent;
        p.private_widthParent =  this.private_widthParent;
        p.private_heightContent =  this.private_heightContent;
        p.private_heightParent =  this.private_heightParent;
        
        return p;
    },
    
    cmd:function(cmd,p1,p2,p3) {
        this.base(cmd,p1,p2,p3);
        if(!this["cmd_"+cmd])
            this.private_cmdBuffer.push([cmd,p1,p2,p3]);
    },
    
    cmd_handleLoad:function() {
    
        inx.taskManager.deleteTasks(this.id());
    
        var initialParams = this.info("initialParams");
        var container = this.info("container");
        var rendered = this.info("rendered");
    
        if(this.el)
            this.el.remove();
            
        var p = initialParams;
        p.id = this.id();
        
        // Обработчики событий не привязываются к данному объекту, поэтому второй раз
        // их регистрировать не нужно        
        p.listeners = [];
        var cmp = inx.cmp.create(p);                
        
        if(rendered) {
            cmp.cmd("render");    
            if(container)
                cmp.cmd("appendTo",container);
        }
            
        for(var i=0;i<this.private_cmdBuffer.length;i++) {
            var c = this.private_cmdBuffer[i];
            inx(this.id()).cmd(c[0],c[1],c[2],c[3]);
        }
        
        cmp.fire("componentLoaded");
    },
    
    info_loaderObj:function() {
        return true;
    }

});