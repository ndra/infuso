// @link_with_parent

inx.css(
    ".inx-tabs-tab{cursor:pointer;vertical-align:bottom;padding:6px 6px;border-radius:5px;}",
    ".inx-tabs-tab:hover{background:#cccccc;}",
    ".inx-tabs-selectedTab{background:#cccccc;font-weight:bold;}",
    ".inx-tabs-close{width:11px;height:11px;cursor:pointer;margin-left:4px;vertical-align:middle;}"
)

inx.tabs.head = inx.panel.extend({

    constructor:function(p) {
        p.style = {
            background:"#ededed",
            padding:5,
            height:"content",
            border:0            
        }
        this.base(p);
    },

    cmd_update:function(items) {
    
        if(!this.__body) {
            return;    
        }
        
        var xx = $("<div>");
        
        for(var i=0;i<items.length;i++) {
            var title = inx(items[i]).info("title");
            title = title ? title+"" : "";

            var e = $("<span>").addClass("inx-tabs-tab inx-core-inlineBlock")
            .appendTo(xx)
            .html(title)
            .data("id",items[i]);

            if(this.owner().info("selected")==items[i]) e.addClass("inx-tabs-selectedTab");
            else e.css({});

            var over = function() { this.src = inx.path("%res%/img/components/tabs/close-hover.gif"); }
            var out = function() { this.src = inx.path("%res%/img/components/tabs/close.gif"); }

            if(inx(items[i]).info("param","closable"))
            var close = $("<img>")
                .attr("src",inx.path("%res%/img/components/tabs/close.gif"))
                .addClass("inx-tabs-close")
                .attr("align","absmiddle")
                .appendTo(e)
                .data("id",items[i])
                .mouseover(over)
                .mouseout(out);
        }
        
        this.cmd("html",xx)
    },

    cmd_render:function(c) {
        this.base(c);
        this.__body.addClass("inx-unselectable");
    },

    cmd_mousedown:function(e) {

        // Если мы попали на кнопку закрыть - убиваем табу и выходим
        var id = $(e.target).parents().andSelf().filter(".inx-tabs-close").data("id");
        if(id) {
            inx(id).cmd("destroy");
            return;
        }

        // Выделяем табу
        var id = $(e.target).parents().andSelf().filter(".inx-tabs-tab").data("id");
        id && this.owner().cmd("handleUserSelect",id);

    }

});
