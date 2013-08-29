// @include inx.file,inx.list

inx.css(
    "esomaiq.{overflow:hidden;padding:10px;text-align:center;width:100px;vertical-align:top;font-size:10px;}"
);

inx.ns("inx.mod.file").files = inx.list.extend({

    constructor:function(p) {
    
        p.layout = "inx.layout.column";
        
        p.style = {
            border:1
        }

        p.tbar = [
            {text:"Добавить файл",icon:"plus","onclick":[this.id(),"showAddDlg"]},"|",
            {icon:"left",onclick:[this.id(),"moveSelectedItemUp"]},
            {icon:"right",onclick:[this.id(),"moveSelectedItemDown"]},
            {icon:"refresh",onclick:[this.id(),"updatePreviews"]},
            "|",
            {text:"Удалить выбранные",icon:"delete","onclick":[this.id(),"deleteFiles"]}
        ];
        
        p.emptyHTML = "<div style='padding:10px;color:gray;' >Нет файлов</div>";
        p.sortable = true;
        
        this.uploader = inx({
            type:"inx.file",
            loader:{cmd:"reflex:storage:upload",storage:p.storage},
            icon:"/file/res/upload.gif",
            text:"Закачать файл",
            dropArea:this,
            listeners:{
                complete:[this.id(),"handleUpload"]
            }
        });
        
        this.base(p);
        
        if(p.value) {
            this.cmd("setValue",p.value);
        }
        
        this.on("itemdblclick","showFile");
    },    
    
    cmd_handleUpload:function(file) {
        this.cmd("addPhoto",file);
    },
    
    cmd_showFile:function(id) {
        var url = this.info("item",id,"photo");
        window.open(url);
    },
    
    cmd_deleteFiles:function() {
        var photos = [];
        var sel = this.info("selection");
        for(var i in this.data) {
            var del = false;
            for(var j in sel) {
                if(this.data[i].id==sel[j]) {
                    del = true;
                }
            }
            if(!del) photos.push(this.data[i]);
        }
        this.cmd("select",[]);
        this.cmd("setData",photos);
    },
    
    cmd_updatePreviews:function() {
        inx(this.private_lastCmd).cmd("destroy");
        var files = [];
        for(var i in this.data) {
            files.push(this.data[i].photo);
        }
        this.private_lastCmd = this.call(
            {cmd:"reflex:storage:getPreviews",files:files},
            [this.id(),"handlePreviews"]);
    },
    
    cmd_handlePreviews:function(previews) {
        for(var i in this.data) {
            this.data[i].data.preview = previews[this.data[i].photo];
        }
        this.cmd("updatePhotos");
    },
    
    info_value:function() {
        var ret = [];
        for(var i in this.data) {
            ret.push(this.data[i].photo);
        }
        return inx.json.encode(ret);
    },
    
    cmd_setValue:function(value) {
    
        value = inx.json.decode(value);
        if(!value) {
            value = [];
        }
        
        var data = [];
        for(var i in value) {
            data.push({
                id:i,
                photo:value[i].f,
                text:Math.random()
            });
        }
            
        this.cmd("setData",data);
        this.cmd("updatePreviews");
    },
    
    cmd_addPhoto:function(value) {
        this.data.push({photo:value,id:inx.id()});
        this.cmd("setData",this.data);
        this.task("updatePreviews");
    },
    
    cmd_updatePhotos:function() {
        this.cmd("setData",this.data);        
    },
    
    info_itemConstructor:function(data) {
        var ret = this.base(data);
        ret.width = 108;
        return ret;
    },
   
    cmd_showAddDlg:function() {
        var dlg = inx({
            type:"inx.mod.file.dlg",
            storage:this.storage,
            listeners:{
                select:[this.id(),"addPhoto"]
            }
        }).cmd("render");
        dlg.setOwner(this);
    },
    
    renderer:function(e,data) {
    
        e.addClass("esomaiq");
        var preview = data.preview;
        if(preview) {
            $("<img>").css({
                width:100,
                height:100,
                display:"block"
            }).attr("src",preview).appendTo(e);
        }
    }

})