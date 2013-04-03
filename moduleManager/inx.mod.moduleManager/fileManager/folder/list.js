// @link_with_parent
// @include inx.list

inx.mod.moduleManager.fileManager.folder.list = inx.list.extend({

    constructor:function(p) {
        if(!p.path) p.path = "/";
        p.type = "inx.list";
        p.loader = {
            cmd:"moduleManager:fileManager:listFiles",
            module:p.module
        };
        this.base(p);
        this.on("itemdblclick",[this.id(),"openItem"]);
        this.on("beforeload",[this.id(),"beforeLoad"]);
        this.on("data",[this.id(),"beforeData"]);
    },
    
    renderer:function(e,data) {
        e.html("");
        e.addClass("inx-core-inlineBlock").addClass("sj49znwg07wa2dca2twu");
        if(data.preview)
            $("<img>").attr("src",data.preview+"").appendTo(e);
        $("<div>").text(data.text+"").appendTo(e);
        return false;
    },
    
    cmd_loadFolder:function(path) {
        if(path==this.path) this.cmd("load");
    },
    
    cmd_beforeData:function(data) {
        data.unshift({
            text:"..",
            id:".."
        })
    },
    
    info_currentPath:function() {
        return this.path;
    },

    info_selectedFiles:function() {
        var sel = this.info("selection");
        var ret = [];
        for(var i in sel)
            ret.push(this.info("item",sel[i]).path);
        return ret;
    },
    
    cmd_setPath:function(path) {
        path = path.replace("//","/");
        this.path = path;
        this.cmd("load");
    },
    
    // Поднимается на один уровень выше
    cmd_stepBack:function() {
        var path = this.path.replace(/[^/]+\/?$/,"");
        this.fire("openFolder",path);
    },
    
    cmd_beforeLoad:function(data) {
        data.path = this.path;
    },
    
    cmd_openItem:function(id) {
        var item = this.info("item",id);
        if(!item) return;
        var path = item.path;
                
        if(item.id=="..") {
            this.cmd("stepBack");
            return;
        }
        
        if(item.dir) {
            this.fire("openFolder",path);
            return;
        }
        
        this.fire("openFile",path);        
        
    }
    
});