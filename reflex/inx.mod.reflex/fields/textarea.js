// @include inx.tabs

inx.ns("inx.mod.reflex.fields").textarea = inx.tabs.extend({

    constructor:function(p) {
    
        p.selectNew = false;
        p.showHead = false;  
        
        p.style = {
            height:"content",
            border:1,
            borderRadius:5
        }
             
        p.items = [{
            type:"inx.mod.reflex.fields.textarea.plain",
            title:"html",
            name:"plain",
            height:"content",
            value:p.value,
            index:p.index,
            lazy:true
        },{
            //type:"inx.mod.reflex.fields.textarea.wysiwyg",
            title:"Визуальный редактор",
            padding:10,           
            html:"Визуальный редактор является экспериментальным и будет доступен в следующих версиях системы.",
            lazy:true
        }]
        this.base(p);
    },
    
    cmd_openFilemanager:function(cmd) {
        inx({
            type:"inx.mod.file.dlg",
            storage:this.storage,
            listeners:{
                select:cmd
            }
        }).cmd("render").setOwner(this);
    },
    
    info_value:function() {    
        var val = this.items().eq("name","plain").info("value");
        return val;
    },
    
    cmd_setValue:function(val) {
        this.items().eq("name","plain").cmd("setValue",val);
    }    
    
})