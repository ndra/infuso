// @include inx.tabs,inx.list,inx.tree
/*-- /moduleManager/inx.mod.moduleManager/fileManager.js --*/

inx.ns("inx.mod.moduleManager").fileManager = inx.panel.extend({

    constructor:function(p) {
        p.layout = "inx.layout.fit";
        p.items = [{
            type:"inx.mod.moduleManager.fileManager.folder",
            basedir:p.basedir,
            viewMode:"tree",
            style:{
                height:"parent"
            }
        }];
        this.base(p);
    }
    
})


/*-- /moduleManager/inx.mod.moduleManager/fileManager/editor.js --*/


inx.mod.moduleManager.fileManager.editor = inx.tabs.extend({

    constructor:function(p) {
        p.title = p.path;
        p.height = parent;
        p.showHead = false;
        this.call({
            cmd:"moduleManager/fileManager/getContents",
            path:p.path
        },[this.id(),"handleContents"]);
        this.base(p);
    },
    
    cmd_handleContents:function(data) {

        this.editor = inx({
            type:"inx.code",
            value:data.code,
            height:"parent",
            lang:data.lang
        });
        
        this.cmd("add",this.editor);
        inx.hotkey("ctrl+s",[this.id(),"save"]);

    },
    
    cmd_save:function() {
    
        if(!this.editor) {
            return false;
        }
        
        this.call({
            cmd:"moduleManager_fileManager:setContents",
            path:this.path,
            module:this.module,
            php:this.editor.info("value"),
        });
        return false;
    },
    
    cmd_updateParams:function(p) {
        this.path = p.path;
        this.name = "file:"+p.path;
        this.cmd("setTitle",p.path);
    }
})

/*-- /moduleManager/inx.mod.moduleManager/fileManager/folder.js --*/


inx.mod.moduleManager.fileManager.folder = inx.panel.extend({

    constructor:function(p) {
    
        p.title = "/"+p.basedir+"/";
        p.layout = "inx.layout.fit";
        
        if(!p.viewMode) {
            p.viewMode = "preview";
        }
            
        if(!p.path) {
            p.path = "";
        }
        
        this.uploader = inx({
            type:"inx.file",
            air:true,
            dropArea:this,
            icon:"upload",
            beforeupload:[this.id(),"beforeUpload"],
            oncomplete:[this.id(),"handleChanges"],
            loader:{cmd:"moduleManager:fileManager:upload"}
        });
        
        p.tbar = [
            this.viewMode,
            {icon:"refresh",onclick:[this.id(),"refresh"],air:true},
            "|",
            {icon:"folder",onclick:[this.id(),"createFolder"],air:true},
            {icon:"file",onclick:[this.id(),"createFile"],air:true},
            {icon:"save",onclick:[this.id(),"pack"],air:true},            
            this.uploader,
            "|",
            {icon:"delete",onclick:[this.id(),"deleteFile"],air:true}
        ]
        
        this.base(p);
        inx.hotkey("f5",[this.id(),"load"]);
        this.cmd("setPath",this.path);
        this.cmd("changeViewMode",p.viewMode);
    },
    
    /**
     * Обновляет выделенную папку
     **/
    cmd_refresh:function() {
        var path = this.items().info("currentPath");
        this.items().cmd("loadFolder",path);
    },
    
    cmd_beforeUpload:function(p) {
        var path = this.items().info("currentPath")
        p.path = path;
    },
    
    cmd_pack:function() {
        var files = this.items().info("selectedFiles");
        if(!files.length) {
            return;
        }
        this.call({
            cmd:"moduleManager:fileManager:pack",
            files:files
        },[this.id(),"handlePack"]);
    },
    
    cmd_handlePack:function(p) {
        window.open(p);
    },
    
    cmd_deleteFile:function() {
    
        if(!confirm("Delete files?")) {
            return;
        }
        
        var files = this.items().info("selectedFiles");
        
        this.call({
            cmd:"moduleManager_fileManager:deleteFiles",
            files:files
        },[this.id(),"handleChanges"]);
    },
    
    cmd_createFolder:function() {
        this.call({
            cmd:"moduleManager:fileManager:newFolder",
            module:this.module,
            path:this.items().info("currentPath")
        },[this.id(),"handleChanges"]);
    },
    
    /**
     * Создает файл в текущей папке
     **/
    cmd_createFile:function() {
        this.call({
            cmd:"moduleManager/fileManager/newFile",
            path:this.items().info("currentPath")
        },[this.id(),"handleChanges"]);
    },
    
    cmd_handleChanges:function(path) {
        this.items().cmd("loadFolder",path);
    },
    
    cmd_changeViewMode:function(mode) {
    
        this.cmd("destroyChildren");
        
        var p = {
            basedir:this.basedir,
            path:this.path,
            listeners:{
                openFile:[this.id(),"openFile"],
                openFolder:[this.id(),"openFolder"]
            }
        };
        
        this.viewMode = mode; 
             
        switch(mode) {
            case "list":
                p.type = "inx.mod.moduleManager.fileManager.folder.list";
                break;
            case "preview":
                p.type = "inx.mod.moduleManager.fileManager.folder.list";
                p.viewMode = "preview";
                break;
            case "tree":
                p.type = "inx.mod.moduleManager.fileManager.folder.tree";
                break;
        }
        this.cmd("add",p);
    },
    
    /**
     * Открывает редактирование папки
     **/
    cmd_openFolder:function(path) {
    
        return;
    
        path = path.replace(/\/$/,"");
        if(this.viewMode=="tree") {
            this.bubble("openEditor",{
                type:"inx.mod.moduleManager.fileManager.editor",
                module:this.module,
                name:"file:"+this.module+":"+path,
                path:path
            })
        } else {
            this.cmd("setPath",path);
        }
    },
    
    /**
     * Открывает редактирование файла
     **/
    cmd_openFile:function(path) {
        this.bubble("openEditor",{
            type:"inx.mod.moduleManager.fileManager.editor",
            module:this.module,
            name:"file:"+this.module+":"+path,
            path:path
        })
    },
    
    cmd_setPath:function(path) {
        this.items().cmd("setPath",path);
        this.path = path;
    }  

})


/*-- /moduleManager/inx.mod.moduleManager/fileManager/folder/tree.js --*/


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

