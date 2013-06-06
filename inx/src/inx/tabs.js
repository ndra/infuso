// @include inx.panel

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