// @link_with_parent
// @include inx.list

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
