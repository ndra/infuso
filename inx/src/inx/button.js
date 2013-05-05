// @include inx.dialog

inx.css(".vbjm1zdmgr-button{cursor:pointer;white-space:nowrap;border:none;float:left;padding:3px 4px 3px 4px;height:16px;border-radius:5px;}");
inx.css(".vbjm1zdmgr-button-hover{box-shadow:0 0 2px rgba(0,0,0,.4) inset;}");
inx.css(".vbjm1zdmgr-button:hover{background:rgba(255,255,255,.4);}");



inx.button = inx.box.extend({

    constructor:function(p) {

        if(p.onclick)
            p.listeners.click = p.onclick;

        p.style = {
            border:0,
            background:"none",
            width:"content"
        }

        p.height = "22";

        this.base(p);

        this.on("click",[this.id(),"showMenu"]);
    },

    cmd_render:function(c) {
        this.base(c);
        this.private_input = $("<div class='vbjm1zdmgr-button' >").appendTo(this.el);
        this.el.addClass("inx-unselectable");        
        this.el.mouseover(inx.cmd(this,"private_handleMouseover"));
        this.cmd("updateHTML");
    },

    cmd_updateHTML:function() {

        if(!this.private_input)
            return;

        // Иконка
        icon = inx.img(this.icon);
        if(icon) {
            this.private_input.css({
                backgroundPosition:"3px center",
                backgroundRepeat:"no-repeat",
                backgroundImage:"url("+icon+")"
            });
            this.private_input.css({paddingLeft:18+(this.text ? 3 : 0)});
        }

        // Текст
        var html = this.text || "";
        if(this.href)
            html = "<a href='"+this.href+"' target='_new'>"+html+"</a>";
        this.private_input.get(0).innerHTML = html;

        this.task("resizeToContents");
    },

    cmd_setIcon:function(icon) {
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

    cmd_private_handleMouseover:function() { this.air && this.cmd("showFrame"); },
    cmd_private_handleMouseout:function() { this.air && this.cmd("hideFrame"); },

    cmd_showFrame:function() {

        this.private_input.addClass("vbjm1zdmgr-button-hover");

        if(this.el.data("__extended")) return;
        this.el.data("__extended",true);
        this.el.click(inx.cmd(this,"private_handleClick"));
        this.el.mouseout(inx.cmd(this,"private_handleMouseout"))
    },

    cmd_hideFrame:function() {
        this.private_input.removeClass("vbjm1zdmgr-button-hover");
    },

    cmd_resizeToContents:function() {
        var a = $("<div/>").addClass("inx-box").appendTo("body");
        var inp = this.private_input.clone().appendTo(a);
        this.cmd("widthContent",inp.get(0).clientWidth,"ya23f9bokv23");
        a.remove();
        this.air || this.task("showFrame");
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
