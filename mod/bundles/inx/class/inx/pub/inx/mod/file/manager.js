// @include inx.list
/*-- /file/inx.mod.file/manager.js --*/


inx.css(
    ".inx-mod-file-storage-preview{overflow:hidden;padding:10px;text-align:center;width:100px;vertical-align:top;font-size:10px;}"
);

inx.ns("inx.mod.file").manager = inx.list.extend({

    constructor:function(p) {
    
        p.layout = "inx.layout.column";
    
        p.loader = {cmd:"reflex:storage:listFiles",storage:p.storage};
        p.listeners = {
            itemdblclick:[this.id(),"handleDblclick"],
            selectionchange:[this.id(),"handleSelectionChange"],
            beforeload:[this.id(),"beforeLoad"]
        }
        
        this.uploader = inx({
            type:"inx.file",
            loader:{cmd:"reflex:storage:upload",storage:p.storage},
            icon:"/file/res/upload.gif",
            text:"Закачать файл",
            dropArea:this,
            listeners:{
                beforeupload:[this.id(),"beforeLoad"],
                complete:[this.id(),"load"]
            }
        });
        
        this.downloadLink = inx({type:"inx.button",text:"Скачать",href:"javascript::void()"});

        if(!p.hideControls)
            p.tbar = [
                this.uploader,
                {text:"Создать папку",onclick:[this.id(),"mkdir"]},
                {text:"Без картинки",onclick:[this.id(),"noimage"]},
                "|",
                {text:"Удалить",onclick:[this.id(),"deleteFile"],icon:"/file/res/delete.png"},
                {type:"inx.panel.separator"},
                this.downloadLink,
            ];
        
        this.path = inx({
            type:"inx.mod.file.manager.breadcrumbs",
            onchange:[this.id(),"load"],
            region:"top"
        });
        
        if(!p.hideControls)
            this.status = inx({
                type:"inx.panel",
                autoHeight:true,
                background:"#ededed",
                region:"bottom"
            });
        
        p.side = [this.path,this.status];
        
        this.url = inx({type:"inx.textfield",width:700});       
        
        if(!p.style) {
            p.style = {};
        }
        p.style.padding = 10;
        p.style.border = 0;
        
        p.emptyHTML = "<div style='color:gray;'>Нет файлов</div>";     
        
        this.base(p);
        this.on("load",[this.id(),"handleLoad"]);
        
        if(!p.disableAutoOpen) {
            this.on("fileSelected",[this.id(),"handleFileSelected"]);
        }
    },
    
    info_itemConstructor:function(data) {
        var ret = this.base(data);
        ret.width = 128;
        return ret;
    },
    
    cmd_noimage:function() {
        this.fire("fileSelected","");
    },
   
    cmd_handleFileSelected:function(name) {
        window.open(name);
    },
    
    cmd_handleLoad:function(data,meta) {
    //    inx(this.status).cmd("html",meta.status);
    },
    
    renderer:function(e,data) {
        e.html("");
        e.addClass("inx-core-inlineBlock").addClass("inx-mod-file-storage-preview");
        $("<img>").css({width:100,height:100}).attr("src",data.icon).appendTo(e);
        $("<div>").text(data.name).appendTo(e);
        return false;
    },
    
    cmd_beforeLoad:function(data) {
        data.path = this.path.info("value");
    },
    
    cmd_handleSelectionChange:function(sel) {
        var node = this.info("item",sel);
        this.url.cmd("setValue",node.url);
        this.downloadLink.cmd("setHref",node.url);
    },
    
    cmd_ok:function() {
        this.fire("fileSelected",this.url.info("value"));
    },
    
    cmd_handleDblclick:function(sel) {
        var node = this.info("item",sel);
        if(node.folder)
            this.cmd("goDeeper",node.name);
        else
            this.fire("fileSelected",node.url);
    },    
    
    cmd_goDeeper:function(name) {
        this.path.cmd("goDeeper",name);
    },
    
    cmd_deleteFile:function() {
    
        var sel = this.info("selection");
        if(!sel.length) { inx.msg("Файл не выбран",1); return; }
        
        var files = [];
        for(var i in sel)
            files.push(this.info("item",sel[i],"name"));
    
        if(!confirm("Удалить файлы ("+files.length+")?")) return;
        
        this.call({
            cmd:"reflex:storage:delete",
            storage:this.storage,
            files:files,
            path:this.path.info("value")
        },[this.id(),"load"]);
    },
    
    cmd_mkdir:function() {        
        var name = prompt("Название папки");
        if(!name) return;
        this.call({
            cmd:"reflex:storage:mkdir",
            storage:this.storage,
            name:name,
            path:this.path.info("value")
        },[this.id(),"load"]);
    },
    
    cmd_keydown:function(e) {
        if(e.keyCode==46) {
            this.cmd("deleteFile");
            return;
        }
        this.base(e);
    }
    
});

/*-- /file/inx.mod.file/manager/breadcrumbs.js --*/


inx.mod.file.manager.breadcrumbs = inx.box.extend({

    constructor:function(p) {
        if(!p)p={};
        p.height = 22;
        p.value = "/";
        this.base(p);
        if(p.onchange) this.on("change",p.onchange);
    },
    
    cmd_render:function(c) {
        this.base(c);
        this.el.css({background:"#ededed"});
        this.cmd("update");
    },
    
    cmd_mousedown:function(e) {
        var path = $(e.target).data("path");
        if(!path) return;
        this.cmd("setValue",path);
    },
    
    cmd_setValue:function(val) {
        this.value = val;
        this.cmd("update");        
        this.fire("change",this.value);        
    },
    
    info_value:function() {
        return this.value;
    },
    
    cmd_goDeeper:function(dir) {
        this.value = this.value+"/"+dir;
        this.cmd("update");
        this.fire("change",this.value);
    },
    
    cmd_update:function() {
    
        if(this.value=="/")
            this.cmd("hide");
        else
            this.cmd("show")
    
        var path = this.value.split("/");
        this.el.html("");
        var e = $("<div>").css({padding:4}).appendTo(this.el);
        e.css({cursor:"pointer"});
        $("<b>[В начало]: </b>").data("path","/").appendTo(e);
        var folder = "/";
        for(var i in path) {
            var piece = path[i];
            if(!piece) continue;
            var s = $("<span>").css({cursor:"pointer"}).html("/"+piece);
            folder += "/"+piece;
            s.data("path",folder);
            s.appendTo(e);
        }

    }

})

