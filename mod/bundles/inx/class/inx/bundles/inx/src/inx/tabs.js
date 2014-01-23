// @include inx.panel
/*-- /mod/bundles/inx/src/inx/tabs.js --*/


inx.tabs = inx.panel.extend({

    constructor:function(p) {
        p.layout = "inx.layout.fit";
        
        if(!p.headComponent) {
            p.headComponent = "inx.tabs.head";
        }
        
        if(!p.side)
            p.side = [];
        p.showHead = p.showHead===undefined ? true : p.showHead;
        p.selectNew = p.selectNew===undefined ? true : p.selectNew;
        if(p.showHead) {
            this.tabs = inx.cmp.create({
                type:p.headComponent,
                region:"top",
                listeners:{
                    select:[this.id(),"handleUserSelect"]
                }
            });
            p.side.push(this.tabs);
        }
        this.base(p);
        if(p.onselect)
            this.on("select",p.onselect);
        inx.storage.onready(this.id(),"handleStorage");        
        
    },

    axis_head:function() {
        return this.tabs;
    },
    
    axis_selected:function() {
        return inx(this.private_selected);
    },

    cmd_handleStorage:function() {
        if(!this.keepSelection) return;
        var name = inx.storage.get(this.keepSelection);
        var id = this.items().eq("name",name).id();
        this.cmd("select",id);
    },

    cmd_hideTabs:function() {
        this.tabs.cmd("hide");
    },

    cmd_selectLast:function() {
        var last = this.items().last().id();
        this.cmd("select",last);
    },

    cmd_select:function(id) {
        var cmp = inx(id);
        if(!cmp.exists())
            return;
        var id = cmp.id();
        if(this.private_selected==id) return;
        this.private_selected = id;
        this.task("updateSelection");
        this.fire("select",id);
        this.task("syncLayout");
    },

    cmd_handleUserSelect:function(id) {
        if(this.keepSelection)
            inx.storage.set(this.keepSelection,inx(id).info("name"));
        this.cmd("select",id);
    },

    cmd_updateSelection:function() {
        
        for(var i=0;i<this.private_items.length;i++) {
            var cmp = inx(this.private_items[i]);
            if(cmp.id()==this.private_selected) {
                cmp.cmd("show");
            }
            else cmp.cmd("hide");
        }
        
        if(this.tabs) {
            this.tabs.cmd("update",this.private_items,this.info("selected"));
        }
    },

    // Возвращает активный компонент
    info_selected:function() {
        return this.private_selected
    },

    cmd_handleComponentLoaded:function() {
        if(this.tabs)
            this.tabs.cmd("update",this.private_items,this.info("selected"));
    },

    cmd_restoreSelection:function() {
        inx.storage.onready(this.id(),"handleStorage");
    },
    
    cmd_addNameExists:function(cmp) {
        this.cmd("select",cmp)
    },
    
    cmd_add:function(cmp,priority) {
    
        cmp = this.base(cmp,priority);
        
        cmp.on("componentLoaded",[this.id(),"handleComponentLoaded"]);
        cmp.on("titleChanged",[this.id(),"handleComponentLoaded"]);
        
        var that = this;
        cmp.on("lazyGoesActive",function(){
            that.cmd("select",this);
        })
        
        // Выбираем добавленный элемент, если необходимо
        if(this.selectNew) {
            this.cmd("select",cmp.id());
        } else {
            if(this.private_items[0]==cmp.id()) {
                this.cmd("select",cmp.id());
            }
        }
        
        this.task("updateSelection");
        return cmp;
    },
    
    cmd_remove:function(cmp) {
        this.task("updateSelection");  
        var id = inx(cmp).id();
        this.base(cmp);
        if(this.info("selected")==id)
            this.cmd("selectLast");        
    }

});

/*-- /mod/bundles/inx/src/inx/tabs/head.js --*/


inx.css(
    ".inx-tabs-tab{cursor:pointer;vertical-align:bottom;padding:6px 6px;border-radius:5px 5px 0 0;margin-top:4px;}",
    ".inx-tabs-tab:hover{background:white;}",
    ".inx-tabs-selectedTab{background:white;font-weight:bold;box-shadow:0 0 10px rgba(0,0,0,.2);}",
    ".inx-tabs-close{width:11px;height:11px;cursor:pointer;margin-left:4px;vertical-align:middle;}"
)

inx.tabs.head = inx.panel.extend({

    constructor:function(p) {
        p.style = {
            background:"#ededed",
            padding:0,
            height:"content",
            border:0            
        }
        this.base(p);
    },

    cmd_update:function(items,selected) {
    
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
        this.__body.css({boxShadow:"0 -5px 10px rgba(0,0,0,.1) inset"});
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
        id && this.fire("select",id);

    }

});


