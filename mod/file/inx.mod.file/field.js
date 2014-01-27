// @include inx.file,inx.list

inx.ns("inx.mod.file").field = inx.box.extend({

    constructor:function(p) {
    
        p.labelAlign = "left";
        p.height = 102;
        p.width = 102;
        
        if(!p.style) {
            p.style = {};
        }
        p.style.border = 1;
        
        this.base(p);
        if(p.value)
            this.cmd("setValue",p.value);
            
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
                    
    },
    
    cmd_destroy:function() {
        this.uploader.cmd("destroy");
        this.base();
    },
    
    cmd_handleUpload:function(file) {
        this.cmd("setValue",file);
        this.cmd("requestPreview");
    },
    
    cmd_render:function(c) {
        this.base(c);
        this.preview = $("<img style='cursor:pointer;' />").appendTo(this.el);
        this.cmd("setValue",this.value);
    },
    
    cmd_requestPreview:function() {
        inx(this.private_lastCmd).cmd("destroy");
        this.private_lastCmd = this.call(
            {cmd:"reflex:storage:getPreview",url:this.info("value")},
            [this.id(),"handlePreview"]);
    },
    
    cmd_handlePreview:function(data) {
        this.preview.attr("src",data);
    },
    
    cmd_setValue:function(value) {
        this.value = value;
        this.cmd("requestPreview");
    },
    
    cmd_handleDlg:function(value) {
        this.cmd("setValue",value);
        this.cmd("focus");
    },
    
    info_value:function() {
        return this.value;
    },
    
    cmd_mousedown:function() {
        var dlg = inx({
            type:"inx.mod.file.dlg",
            storage:this.storage,
            listeners:{
                select:[this.id(),"handleDlg"]
            }
        }).cmd("render");
        dlg.setOwner(this);
    }

})