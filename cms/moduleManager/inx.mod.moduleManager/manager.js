// @include inx.viewport,inx.tabs,inx.tree
// @include inx.mod.moduleManager.fileManager

inx.ns("inx.mod.moduleManager").manager = inx.viewport.extend({

    constructor:function(p) {
        
        // Боковые панели
        this.tree = inx({
            type:"inx.tree",
            resizable:true,
            region:"right",
            width:200,
            showRoot:false,
            data:p.tree,
            listeners:{
                selectionchange:[this.id(),"selectionChange"]
            },
            keepExpanded:"z7f9rtp1hn8ui78v0o4g",
            tbar:[{
                text:"Build",
                icon:"ok",
                onclick:[this.id(),"buildProject"]
            }]
        });
        
        this.right = inx({
            type:"inx.tabs",
            showHead:false,
            region:"right",
            width:200,
            resizable:true
        });
        
        p.side = [this.tree,this.right];
        
        p.keepLayout = "fui3wliqkgi389bqp1rx";
        
        // Табы
        this.tabs = inx({
            height:"parent",
            type:"inx.tabs"
        });
        p.items = [this.tabs];
        
        this.base(p);
        inx.hotkey("ctrl+w",[this.id(),"closeActiveTab"]);
        inx.hotkey("f1",[this.id(),"toggleNavigation"]);
        inx.hotkey("f5",[this.id(),"doNothing"]);
        
        $(window).get(0).onbeforeunload = function() { return false; };
        
        this.on("openEditor","openEditor");
    },
    
    cmd_buildProject:function() {
        this.call({
            cmd:"moduleManager:build"
        });
    },
    
    cmd_doNothing:function() {
        return false;
    },
    
    cmd_toggleNavigation:function() {
        var cmd = this.right.info("hidden") ? "show" : "hide";
        this.tree.cmd(cmd);
        this.right.cmd(cmd);
        return false;
    },    
    
    cmd_closeActiveTab:function() {
        var selected = this.tabs.info("selected");
        inx(selected).cmd("destroy");
        return false;
    },
    
    cmd_selectionChange:function(a) {
    
        var node = this.tree.info("node",a[0]);
        
        var editor = node.editor;
        editor.name = node.id;
        
        editor.listeners = {
            openEditor:[this.id(),"openEditor"],
            closeEditor:[this.id(),"closeEditor"],
            changeParams:[this.id(),"changeParams"]                
        };
        
        this.right.cmd("add",editor);
    },
    
    cmd_openEditor:function(p) {
        p.closable = true;
        this.tabs.cmd("add",p);
    },
    
    cmd_openFind:function() {
        this.tabs.cmd("add",{
            type:"inx.mod.moduleManager.fileManager.folder",
            name:"find"
        });
    },
    
    cmd_closeEditor:function(name) {
        this.tabs.items().eq("name",name).cmd("destroy");
    },    
    
    cmd_changeParams:function(name,p) {
        this.tabs.items().eq("name",name).cmd("updateParams",p);
    }
})
