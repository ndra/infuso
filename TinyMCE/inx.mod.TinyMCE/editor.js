// @include inx.dialog

inx.ns("inx.mod.TinyMCE").editor = inx.panel.extend({

    constructor:function(p) {
        if(!p.value)
            p.value = "";
        if(!p.height) 
            p.height = 250;
        p.autoHeight = false;
        if(!p.mode)
            p.mode = "adv";
        if(!p.labelAlign)
            p.labelAlign = "top";
        this.base(p);
    },    

    cmd_render:function() {
        this.base();
        this.__body.css("overflow","hidden");
        this.contents = $("<textarea style='width:100%;border:none;color:white;'>").appendTo(this.__body);
        this.contents.attr("id",this.id());
        inx.mod.TinyMCE.editor.loader.load(inx.delegate(this.initTinyMCE,this));
    },
    
    info_value:function() {
        if(!window.tinyMCE)
            return this.value;
        inst = tinyMCE.get(this.id())
        if(inst)
            return tinyMCE.get(this.id()).getContent();
        else
            return this.value;
    },

    cmd_destroy:function() {
        if(window.tinyMCE && window.tinyMCE.get(this.id()))
            window.tinyMCE.get(this.id()).destroy();
        this.base();
    },
    
    cmd_setValue:function(c) {
        if(!window.tinyMCE)
            return;
        var inst = tinyMCE.get(this.id())
        if(inst)
            tinyMCE.get(this.id()).setContent(c+"");
    },
    
    cmd_syncLayout:function() {    
        this.base();    
        if(!window.tinyMCE)
            return;
            
        var inst = tinyMCE.get(this.id());        
        if(!inst)
            return;
            
        var theme = inst.theme;
        // Что-то глючит внутри тини, поэтому в трайкач
        try {
            theme.resizeTo("100%",this.__bodyHeight);
        } catch(ex) {
        }
    },
    
    cmd_iaa:function() {
        this.initTinyMCE();
    },

    initTinyMCE:function() {
    
        var c = $("#"+this.id());
        if(!c.length) {
        
            var cmpid = this.id();
            var fn = function() {
                inx(cmpid).cmd("iaa");
                inx(cmpid).cmd("initTinyMCE");
            }
            setTimeout(fn,500);
            return;
        }
        
    
        this.contents.attr("value",this.info("value"));
        this.contents.height(this.info("height")-15);
        
        var base = {
            mode : "exact",
            theme : "advanced",
            elements:this.id(),
            convert_urls : false,
            force_br_newlines : true,
            force_p_newlines : false,
            forced_root_block : "",
            elements:this.id(),
            file_browser_callback:inx.delegate(this.triggerFilemanager,this)
        };
        
        var adv = {
            plugins : "pagebreak,table,advhr,advimage,advlink,inlinepopups,insertdatetime,paste,fullscreen,visualchars,nonbreaking",
            theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,formatselect,fontselect,fontsizeselect",
            theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
            theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,fullscreen,visualchars,nonbreaking,pagebreak"
        };
        
        var simple = {
            theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull",
            theme_advanced_buttons2: ""
        };
        
        if(this.mode=="adv")
            for(var i in adv)
                base[i] = adv[i];
        else
            for(var i in simple)
                base[i] = simple[i];

        tinyMCE.init(base);        
        
    },

    triggerFilemanager:function(field_name, url, type, win) {
        var callback = function(value) {
            win.document.forms[0].elements[field_name].value = value;
        }
        this.fire("filemanager",callback);
    } 
    
    
});