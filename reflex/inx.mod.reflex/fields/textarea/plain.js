// @include inx.panel,inx.textarea
// @link_with_parent

inx.mod.reflex.fields.textarea.plain = inx.panel.extend({
    constructor:function(p) {
        this.textarea = inx({
            type:"inx.textarea",
            height:"content",
            value:p.value,
            style:{
                border:0
            }
        });
        p.padding = 0;
        p.bbar = [            
            {text:"Заголовок",onclick:[this.id(),"h2"],air:true},
            {text:"<b>Жирный</b>",onclick:[this.id(),"b"],air:true},
            {text:"<i>Курсив</i>",onclick:[this.id(),"i"],air:true},
            "|",
            {text:"Ссылка",onclick:[this.id(),"href"],air:true},
            {text:"Фото",onclick:[this.id(),"image"],air:true},
            {text:"Фaйл",onclick:[this.id(),"file"],air:true},
            {text:"Виджет",onclick:[this.id(),"widget"],air:true},
            "|",
            {text:"Еще",air:true,icon:"gear",menu:[
                {text:"Типографить",onclick:[this.id(),"typografize"],air:true},
                {text:"Удалить тэги",onclick:[this.id(),"clearHTML"],air:true},
                {text:"Закачать внешние файлы",onclick:[this.id(),"downloadExternalFiles"],air:true}
            ]}
        ];
        p.items = [this.textarea]
        this.base(p);
        this.textarea.on("focus",[this.id(),"handleFocus"]);
        this.on("smoothBlur",[this.id(),"handleBlur"]);
        this.bbar.cmd("hide");
    },
    
    cmd_handleFocus:function() {
        this.textarea.task("focus");

        try {
            clearTimeout(this.focusActionTimeout);
        } catch(ex) {}
        this.focusAction = "show";
        this.focusActionTimeout = setTimeout(inx.cmd(this,"doFocusAction"),500);
    },
    
    cmd_handleBlur:function() {
        
        try {
            clearTimeout(this.focusActionTimeout);
        } catch(ex) {}
        this.focusAction = "hide";
        this.focusActionTimeout = setTimeout(inx.cmd(this,"doFocusAction"),500);
    },
    
    cmd_doFocusAction:function() {
        this.bbar.cmd(this.focusAction);
    },
    
    cmd_handleData:function(data) {
        if(data) this.cmd("setValue",data);
    },
    
    info_value:function() {
        return this.textarea.info("value");
    },
    
    cmd_setValue:function(value) {
        this.textarea.cmd("setValue",value);
    },    
    
    cmd_widget:function(value) {
        inx({
            type:"inx.mod.reflex.fields.textarea.widget",
            listeners:{
                selectWidget:[this.id(),"placeWidget"]
            }
        }).setOwner(this).cmd("render");
    },
    
    cmd_placeWidget:function(data) {
        var open = "<widget ";
        for(var i in data.params)
            open+= i+"='"+data.params[i]+"'";
        open+= ">";
        var close = "</widget>";
        
        this.cmd("replace",open,"</widget>");
    },

    cmd_replace:function(prefix,suffix) {        
        var src = this.info("value");
        var caret = this.textarea.info("caret");
        var a = src.substr(0,caret.start);
        var b = src.substr(caret.start,caret.end-caret.start);
        var c = src.substr(caret.end,src.length-caret.end);
        this.cmd("setValue",a+prefix+b+suffix+c);
        this.textarea.cmd("setCaret",(a+prefix).length,(a+prefix+b).length);
    },
    
    cmd_enableWYSIWYG:function() {
    },

    cmd_typografize:function() {
        this.call({
            cmd:"reflex/editor/fieldController/textfield/typograph",
            text:this.info("value")
        },[this.id(),"handleData"]);
    },
    
    cmd_downloadExternalFiles:function() {
        this.call({
            cmd:"reflex/editor/fieldController/textfield/downloadExternalFiles",
            text:this.info("value"),
            index:this.index
        },[this.id(),"handleData"]);
    },
    
    cmd_clearHTML:function() {
        if(!confirm("Удалить тэги?")) return;
        this.call({
            cmd:"reflex/editor/fieldController/textfield/cleanup",
            text:this.info("value")
        },[this.id(),"handleData"]);
    },
    
    cmd_h2:function() { this.cmd("replace","<h2>","</h2>"); },
    cmd_b:function() { this.cmd("replace","<b>","</b>"); },
    cmd_i:function() { this.cmd("replace","<i>","</i>"); },
    cmd_image:function() { this.owner().cmd("openFilemanager",[this.id(),"insertImage"]); },
    cmd_file:function() { this.owner().cmd("openFilemanager",[this.id(),"insertFile"]); },
    
    cmd_href:function() {
        var href = prompt("Введите адрес ссылки");
        if(!href) return;
        this.cmd("replace","<a href='"+href+"' >","</a>");
    },
    
    cmd_insertImage:function(img) { this.cmd("replace","<img src='"+img+"' />",""); },
    cmd_insertFile:function(file) { this.cmd("replace","<a href='"+file+"' >","</a>"); }
    
})