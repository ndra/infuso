// @link_with_parent
// @include inx.tree

inx.mod.moduleManager.fileManager.folder.tree = inx.tree.extend({

    constructor:function(p) {
    
        if(!p.path) p.path = "/";
        p.root = {path:"/"};
        p.loader = {
            cmd:"moduleManager:fileManager:listFiles",
            module:p.module
        };
        p.keepSelectionField = "text";
        if(!p.listeners) p.listeners = {};
        p.listeners.beforeload = function(data) {data.path = this.info("path",data.id)};
        this.base(p);
        this.on("dblclick",[this.id(),"openItem"]);
        this.on("editComplete","handleRename");
    },    
    
    cmd_handleRename:function(id,newName,old) {
        var node = this.info("node",id);
        var path = node.path;
        path = path.split("/");
        path.pop();
        path.push(old);
        var oldPath = path.join("/");
        var newPath = this.info("path",id);
        this.call({cmd:"moduleManager_fileManager:renameFile",module:this.module,old:oldPath,"new":newPath},[this.id(),"handleChanges"]);
    },
    
    cmd_handleChanges:function(data) {
        this.cmd("loadFolder",data);
    },
    
    cmd_loadFolder:function(path) {
        this.cmd("eachNode",function(node) {
            if(node.path==path)
                this.cmd("load",node.id);
        })
    },

    cmd_openItem:function(id) {
        var item = this.info("node",id);
        if(!item) return;
        
        // Для того, чтобы корень открывался как папка, а не как файл
        if(!item.id) item.dir = true;
        
        if(item.dir)
            this.fire("openFolder",item.path);
        else
            this.fire("openFile",item.path);
           
    },
    
    info_currentPath:function() {
        var sel = this.info("selection")[0];
        if(!sel) return "";
        var path = this.info("node",sel).path;        
        return path;
    },
    
    info_selectedFiles:function() {
        var sel = this.info("selection");
        var ret = [];
        for(var i in sel)
            ret.push(this.info("node",sel[i]).path);
        return ret;
    }

});