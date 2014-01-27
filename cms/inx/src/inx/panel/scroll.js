// @link_with_parent

inx.panel = inx.panel.extend({

    info_VScrollRate:function() {
    
        var content = this.info("contentHeight") + this.style("padding")*2;
        var body = this.info("bodyHeight");
        if(content==body) {
            return 1;
        }
    
        return 1 / content * body;
    },
    
    info_HScrollRate:function() {
        return 1 / (this.info("contentWidth") + this.style("padding")*2) * this.info("bodyWidth");
    },

    // Перетаскивание вертикального скроллбара
    cmd_handleDragScroll:function(e) {
        if(e.phase=="start") {
            this.pezxw9i3p = this.info("scrollTop");
        }        
        var k = this.info("VScrollRate");
        var vscroll = this.pezxw9i3p + e.ay / k;
        this.cmd("scrollTop",vscroll);
        this.task("updateVScroll")
    },
    
    cmd_handleDragHScroll:function(e) {
        var k = this.info("HScrollRate");
        this.cmd("scrollLeft",e.dx/k,true);
        this.task("updateHScroll")
    },

    cmd_mousewheel:function(deltha,e) {
    
        if(!this.style("vscroll")) {
            this.owner().cmd("mousewheel",deltha,e);
            return;
        }
            
        this.cmd("scrollTop",-deltha,{add:true,bubble:true});
        return false;
    },
    
    cmd_updateScroll:function() {
        this.cmd("updateVScroll");
        this.cmd("updateHScroll");
    },
    
    /**
     * Устанавливает вертикальный скролл
     **/
    cmd_scrollTop:function(vscroll,params) {
    
        if(!this.style("vscroll")) {
            return;
        }

        if(!params)
            params = {};

        if(params.add) {
            this.private_scrollTop = this.info("scrollTop") + vscroll;
            if(params.bubble) {
                if(vscroll<0 && this.private_scrollTop<=0) {
                    this.owner().cmd("scrollTop",vscroll,params);
                    if(!this.owner().exists())
                        params.xxx = true;
                }
                if(vscroll>0 && this.private_scrollTop >= this.info("maxVScroll")) {
                    this.owner().cmd("scrollTop",vscroll,params);
                    if(!this.owner().exists())
                        params.xxx = true;
                }
            }
        } else {
            this.private_scrollTop = vscroll;
        }

        this.fire("scroll");
        this.task("updateVScroll");

    },

    info_maxVScroll:function() {
        return this.info("contentHeight") + this.style("padding")*2 - this.info("bodyHeight");
    },
    
    info_scrollTop:function() {
        var vscroll = this.private_scrollTop;
        var maxscroll = this.info("maxVScroll");
        if(vscroll>maxscroll)
            vscroll = maxscroll;
        if(vscroll<0)
            vscroll = 0;
        return vscroll;
    },
    
    info_scrollLeft:function() {
        var scroll = this.private_scrollLeft;
        var maxscroll = this.info("contentWidth") + this.style("padding")*2 - this.info("bodyWidth");
        if(scroll>maxscroll)
            scroll = maxscroll;
        if(scroll<0)
            scroll = 0;
        return scroll;
    },
    
    cmd_scrollLeft:function(scroll,add) {    
    
        if(add) {
            this.private_scrollLeft = this.info("scrollLeft") + scroll;
        } else {
            this.private_scrollLeft = scroll;
        }
    
        this.cmd("clearInfoBuffer");
        this.fire("scroll");
        this.task("updateHScroll");        
    },

    cmd_updateVScroll:function() {    
        
        if(!this.__body)
            return;
        
        if(!this.info("contentHeight"))
             return;
    
        var vscroll = this.info("scrollTop");
        
        if(!this.private_scrollbar) {
            this.private_scrollbar = $("<div>")
                .addClass("wu2qcu0xke-scrollbar")
                .attr("olo:lo",this.id())
                .appendTo(this.private_bodyContainer);
            inx.dd.enable(this.private_scrollbar,this,"handleDragScroll",{offset:0})
        }
            
        var k = this.info("VScrollRate");
        
        if(k>=1 && this.private_scrollbarShown) {
            this.private_scrollbar.stop(true).animate({opacity:0},"fast",function() { $(this).css("display","none") });
            this.private_scrollbarShown = false;
        }
        
        if(k<1 && !this.private_scrollbarShown) {
            this.private_scrollbar.css("display","block").stop(true).animate({opacity:1},"fast");
            this.private_scrollbarShown = true;
        }
        
        var h = this.info("bodyHeight") * k;
        var top = vscroll*k;
            
        this.private_scrollbar.css({
            right:0,
            top:top,
            height:h
        })
        
        this.cmd("vscrollUpdateContent",vscroll);
                
    },
    
    cmd_vscrollUpdateContent:function(scroll) {
        this.__body.css({
            marginTop:-scroll
        })
    },    
    
    cmd_updateHScroll:function() {   
    
        if(!this.__body)
            return;
        
        if(!this.info("contentWidth"))
             return;
             
        var scroll = this.info("scrollLeft");

        if(!this.private_hscrollbar) {
            this.private_hscrollbar = $("<div>")
                .addClass("wu2qcu0xke-hscrollbar")
                .appendTo(this.private_bodyContainer);
            inx.dd.enable(this.private_hscrollbar,this,"handleDragHScroll")
        }
            
        var k = this.info("HScrollRate");
                
        if(k>=1 && this.private_hscrollbarShown) {
            this.private_hscrollbar.stop(true,true).animate({opacity:0},"fast",function() { $(this).css("display","none") });
            this.private_hscrollbarShown = false;
        }
        
        if(k<1 && !this.private_hscrollbarShown) {
            this.private_hscrollbar.css("display","block").stop(true,true).animate({opacity:1},"fast");
            this.private_hscrollbarShown = true;
        }
        
        var size = this.info("bodyWidth") * k;
        var offset = scroll*k;
            
        this.private_hscrollbar.css({
            bottom:0,
            left:offset,
            width:size
        })
        
        this.__body.css({
            marginLeft:-scroll
        })
                
    },

    cmd_scrollTo:function(cmp) {
        cmp = inx(cmp);
        var e = cmp.info("param","el");
        if(!e)
            return;
        var t1 = $(e).offset().top;
        var t2 = this.__body.offset().top;
        var min = t1 - t2;
        var max = min - this.info("clientHeight") - this.style("padding") + cmp.info("height");
        var s = this.info("scrollTop");

        if(max>s)
            s = max;
        
        if(min<s)
            s = min;
        
        this.cmd("scrollTop",s);
    }
    
});
