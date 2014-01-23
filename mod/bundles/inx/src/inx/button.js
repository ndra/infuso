// @include inx.dialog

inx.css(".vbjm1zdmgr-button{cursor:pointer;white-space:nowrap;border:none;float:left;border-radius:3px;}");
inx.css(".vbjm1zdmgr-button-hover{box-shadow:0 0 2px rgba(0,0,0,.4) inset;}");
inx.css(".vbjm1zdmgr-button:hover{background:rgba(255,255,255,.4);}");



inx.button = inx.box.extend({

    constructor:function(p) {
    
        if(p.icon) {
            p.icon = inx.img(p.icon);
        }

        if(p.onclick)
            p.listeners.click = p.onclick;

        if(!p.style) {
            p.style = {};
        }
        
        p.style.border = 0;
        
        if(p.style.background===undefined) {
            p.style.background = "none";
        }
        
        p.style.width = "content";
        
        if(!p.style.padding) {
            p.style.padding = 4;
        }

        if(!p.height && !p.style.height) {
            p.height = "22";
        }
        
        p.style.borderRadius = 3;

        this.base(p);

        this.on("click",[this.id(),"showMenu"]);
    },

    cmd_render:function(c) {
        this.base(c);
        this.private_input = $("<div class='vbjm1zdmgr-button' >").appendTo(this.el);     
        this.el.mouseover(inx.cmd(this,"private_handleMouseover"));
        this.cmd("updateHTML");
        if(!this.air) {
            this.cmd("showFrame");
        }
    },

    cmd_updateHTML:function() {

        if(!this.private_input) {
            return;
        }

        // Текст
        var html = this.text || "";
        if(this.href) {
            html = "<a href='"+this.href+"' target='_new'>"+html+"</a>";
        }
        
        this.private_input.get(0).innerHTML = html;

        this.task("resizeToContents");
    },

    cmd_setIcon:function(icon) {
        if(icon) {
            icon = inx.img(icon);
        }
        this.icon = icon;
        this.task("updateHTML");
    },

    cmd_setHref:function(href) {
        this.href = href;
        this.task("updateHTML");
    },

    cmd_setText:function(text) {
        this.text = text;
        this.task("updateHTML");
    },
    
    cmd_syncLayout:function() {
     
        var h = this.info("height") - 2;
        var fontSize = this.style("fontSize");
        var paddingTop = Math.round((h-fontSize)/2);
        var padding = this.style("padding");
        
        var iconWidth = this.style("iconWidth");
        var iconHeight = this.style("iconHeight");
        var paddingLeft = padding;      
        
        if(this.icon) {
        
            switch(this.style("iconAlign")) {
            
                case "left":
                    paddingLeft += iconWidth;            
                    
                    if(this.text) {
                        paddingLeft += 2;
                    }
                    
                    this.private_input.css({
                        backgroundPosition:this.style("padding") + "px center",
                        backgroundRepeat:"no-repeat",
                        backgroundImage:"url("+this.icon+")"
                    });                    
                    break;
                    
                case "top":
                    paddingTop = Math.round((h + iconHeight - fontSize)/2);
                    this.private_input.css({
                        backgroundPosition:"center " + (paddingTop-iconHeight) + "px",
                        backgroundRepeat:"no-repeat",
                        backgroundImage:"url("+this.icon+")",
                        minWidth:iconHeight + padding * 2,
                        textAlign:"center"
                    });                    
                    break;
            }
            
        }
        
        this.private_input.css({
            height:h - paddingTop + 2,
            fontSize:fontSize,
            paddingTop:paddingTop,
            paddingLeft:paddingLeft,
            paddingRight:padding
        });
        
        this.cmd("resizeToContents");
    },

    cmd_private_handleMouseover:function() {
        this.air && this.cmd("showFrame");
    },
    
    cmd_private_handleMouseout:function() {
        this.air && this.cmd("hideFrame");
    },

    cmd_showFrame:function() {
        this.private_input.addClass("vbjm1zdmgr-button-hover");
        if(this.el.data("__extended")) return;
        this.el.data("__extended",true);
        this.el.mouseout(inx.cmd(this,"private_handleMouseout"))
    },

    cmd_hideFrame:function() {
        this.private_input.removeClass("vbjm1zdmgr-button-hover");
    },

    cmd_resizeToContents:function() {
        var width = inx.width(this.private_input,"client");
        this.cmd("widthContent",width);
    },

    cmd_showMenu:function() {
        if(!this.menu) return;
        if(!inx(this.private_menu).exists()) {
            this.private_menu = inx({
                type:"inx.menu",
                items:this.menu
            }).cmd("render");
            this.private_menu.setOwner(this);
        }
        this.private_menu.cmd("show").cmd("focus");
    },

    cmd_destroy:function() {
        this.private_menu && this.private_menu.cmd("destroy");
        this.base();
    }

});
