// @include inx.mod.TinyMCE.editor
// @include inx.tabs

inx.ns("inx.mod.TinyMCE").field = inx.tabs.extend({

    constructor:function(p) {
    
        p.items = [{
            type:"inx.mod.reflex.fields.textarea",
            title:"html",
            name:"html",
            storage:p.storage,
            value:p.value
        },{
            type:"inx.mod.reflex.fields.textarea",
            title:"Визуальный редактор",
            name:"wysiwyg",
            value:p.value,
            type:"inx.mod.TinyMCE.editor",
            listeners:{
                filemanager:[this.id(),"openFilemanager"]
            }
        }]
        
        p.active = "html";
        p.selectNew = false;
                
        this.on("select",[this.id(),"handleSelect"]);
        
        this.base(p);
    },
    
    cmd_handleSelect:function() {
        var active = this.items().eq("name",this.active);
        var current = inx(this).axis("selected");
        current.cmd("setValue",active.info("value"));
        this.active = current.info("name");
    },
    
    cmd_openFilemanager:function(callback) {
        inx({
            type:"inx.mod.file.dlg",
            storage:this.storage,
            listeners:{
                select:function(name){callback(name)}
            }
        }).cmd("render");
    },
    
    info_value:function() {    
        return inx(this).axis("selected").info("value");
    }

});
