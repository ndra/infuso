// @link_with_parent
// @include inx.list

inx.css(
    ".sj49znwg07wa2dca2twu{overflow:hidden;border:1px dotted #ededed;padding:10px;margin:10px;text-align:center;width:100px;vertical-align:top;font-size:10px;}"
);

inx.mod.moduleManager.fileManager.folder = inx.panel.extend({

    constructor:function(p) {
    
        p.title = "/"+p.module+"/";
        p.layout = "inx.layout.fit";
        
        if(!p.viewMode)
            p.viewMode = "preview";
        if(!p.path)
            p.path = "";
        
        this.path = inx({
            type:"inx.mod.moduleManager.fileManager.folder.path",
            region:"top"
        });
        
        this.viewMode = inx({
            type:"inx.select",
            width:80,
            onchange:[this.id(),"changeViewMode"],
            value:p.viewMode,
            data:[
                {id:"tree",text:"Дерево"},
                {id:"preview",text:"Превью"}
            ]
        });
        
        this.uploader = inx({
            type:"inx.file",
            dropArea:this,
            icon:"upload",
            beforeupload:[this.id(),"beforeUpload"],
            oncomplete:[this.id(),"handleChanges"],
            loader:{cmd:"moduleManager:fileManager:upload"}
        });
        
        p.tbar = [
            this.viewMode,
            "|",
            {icon:"folder",onclick:[this.id(),"createFolder"]},
            {icon:"file",onclick:[this.id(),"createFile"]},
            {icon:"save",onclick:[this.id(),"pack"]},
            this.uploader,
            "|",
            {icon:"delete",onclick:[this.id(),"deleteFile"]}
        ]
        
        //p.side = [this.path];
        
        this.base(p);
        inx.hotkey("f5",[this.id(),"load"]);
        this.cmd("setPath",this.path);
        this.cmd("changeViewMode",p.viewMode);
        this.cmd("setPath",p.path);
    },
    
    cmd_beforeUpload:function(p) {
        var path = this.items().info("currentPath")
        p.path = path;
        p.module = this.module;
    },
    
    cmd_pack:function() {
        var files = this.items().info("selectedFiles");
        if(!files.length) return;
        this.call({
            cmd:"moduleManager:fileManager:pack",
            files:files,
            module:this.module
        },[this.id(),"handlePack"]);
    },
    
    cmd_handlePack:function(p) {
        window.open(p);
    },
    
    cmd_deleteFile:function() {
        if(!confirm("Delete files?")) return;
        var files = this.items().info("selectedFiles");
        this.call(
            {cmd:"moduleManager_fileManager:deleteFiles",module:this.module,files:files},
            [this.id(),"handleChanges"]
        );
    },
    
    cmd_createFolder:function() {
        this.call({
            cmd:"moduleManager:fileManager:newFolder",
            module:this.module,
            path:this.items().info("currentPath")
        },[this.id(),"handleChanges"]);
    },
    
    cmd_createFile:function() {
        this.call({
            cmd:"moduleManager:fileManager:newFile",
            module:this.module,
            path:this.items().info("currentPath")
        },[this.id(),"handleChanges"]);
    },
    
    cmd_handleChanges:function(path) {
        this.items().cmd("loadFolder",path);
    },
    
    cmd_changeViewMode:function(mode) {
        this.cmd("destroyChildren");
        var p = {
            module:this.module,
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
    
    cmd_openFolder:function(path) {
        path = path.replace(/\/$/,"");
        if(this.viewMode=="tree")
            this.bubble("openEditor",{
                type:"inx.mod.moduleManager.fileManager.editor",
                module:this.module,
                name:"file:"+this.module+":"+path,
                path:path
            })
        else
            this.cmd("setPath",path);
    },
    
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
