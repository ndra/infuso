// @link_with_parent
// @include inx.tree

inx.mod.moduleManager.fileManager.folder.tree = inx.tree.extend({

    constructor:function(p) {
    
        if(!p.basedir) {
            p.basedir = "/";
        }
        
        p.root = {
            path: p.basedir
        };
        
        p.loader = {
            cmd:"moduleManager/fileManager/listFiles",
            basedir:p.basedir
        };
        
        if(!p.listeners) {
            p.listeners = {};
        }
        
        p.editKey = "name";
        
        p.loadOnEachExpand = true;
        
        p.listeners.beforeload = function(data) {
            var node = this.info("node",data.id)
            data.path = node.relpath;
        };
        
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
        var newPath = this.basedir + "/" + this.info("path",id,{
            key:"name"
        });
        this.call({
            cmd:"moduleManager/fileManager/renameFile",
            old:oldPath,
            "new":newPath
        },[this.id(),"handleChanges"]);
    },
    
    cmd_handleChanges:function(data) {
        this.cmd("loadFolder",data);
    },
    
    /**
     * Перезагружает папку с путем path
     **/
    cmd_loadFolder:function(path) {
        this.cmd("eachNode",function(node) {
        
            var path1 = node.path.replace(/^\//, "").replace(/\/$/, "");
            var path2 = path.replace(/^\//, "").replace(/\/$/, "");
        
            if(path1 == path2) {
                this.cmd("load",node.id);
            }
        })
    },

    /**
     * Открывает редактирования сообщения
     **/
    cmd_openItem:function(id) {
    
        var item = this.info("node",id);
        if(!item) {
            return;
        }
        
        // Для того, чтобы корень открывался как папка, а не как файл
        if(!item.id) {
            item.dir = true;
        }
        
        if(item.dir) {
            this.fire("openFolder",item.path);
        } else {
            this.fire("openFile",item.path);
        }
           
    },
    
    info_currentPath:function() {
        var sel = this.info("selection")[0];
        if(!sel) {
            return "";
        }
        var path = this.info("node",sel).path;        
        return path;
    },
    
    info_selectedFiles:function() {
        var sel = this.info("selection");
        var ret = [];
        for(var i in sel) {
            ret.push(this.info("node",sel[i]).path);
        }
        return ret;
    }

});