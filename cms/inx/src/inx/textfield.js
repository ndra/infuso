inx.css(
    ".dfehopg6{box-shadow:0 5px 10px rgba(0,0,0,.1) inset ;padding:0px;margin:0px;width:100%;border:none;background:none;outline:none;}"
);

inx.textfield = inx.box.extend({

    constructor:function(p) {
    
        if(!p.labelAlign) {
            p.labelAlign = "left";
        }
            
        if(!p.height) {
            p.height = 22;
        }
        
        if(!p.width) {
            p.width = 300;
        }
            
        if(p.value===undefined)
            p.value = "";
            
        p.defaultStyle = {
            border:1,
            background:"white",
            border:1,
            borderRadius:3
        }       
        
        if(!p.autocreate) {
            p.autocreate = p.password ? "<input type='password' class='dfehopg6' />" : "<input class='dfehopg6' />";
        }

        this.caret = {start:0,end:0};
        
        this.base(p);
        
        if(p.onchange) {
            this.on("change",p.onchange);            
        }        
        
        if(p.value!==undefined) {
            this.suspendEvents();
            this.cmd("setValue",p.value);
            this.unsuspendEvents();        
        }
        
    },
    
    cmd_render:function() {
    
        var val = this.info("calculatedTextValue");
    
        this.base();
        
        this.input = $(this.autocreate).appendTo(this.el)
            .focus(inx.textfield.focus)
            .blur(inx.textfield.blur);
        
        this.suspendEvents();
        this.cmd("setTextValue",val);
        this.unsuspendEvents();
        
        // Фокусируемся на элементе и выделяем текст
        if(this.id()==inx.focusManager.cmp().id()) {
            this.input.focus();
            if(this.private_select) {
                this.cmd("select");
            }
        }
        
        if(this.buttons) {
            for(var i in this.buttons) {
                var c = $("<div>").appendTo(this.el).css({position:"absolute",top:-1});
                var button = this.buttons[i];
                button.type = "inx.button";
                button.air = true;
                this.buttons[i] = inx(this.buttons[i]).cmd("render").cmd("appendTo",c).setOwner(this);
                this.buttons[i].data("el",c);
            }
        }
    },    
    
    /**
     * Метод, срабатывающий при изменении фокуса
     * flag = true - фокус
     * flag = false - блюр
     **/
    cmd_handleFocusChange:function(flag) {
    
        if(flag) {
            !this.a && (this.a = setInterval(inx.cmd(this,"checkChanges"),500));
            !this.b && (this.b = setInterval(inx.cmd(this,"keepCaret"),400));
            this.input && this.input.focus();            
        } else {
            this.a && clearInterval(this.a);
            this.a = null;
            this.b && clearInterval(this.b);
            this.b = null;
            this.input && this.input.blur();
            this.cmd("checkChanges");
        }
        
        this.base(flag);
    },
    
    /**
     * Проверяет, не изменилось ли значение поле (текстовое и обычое) по сравнению с прошлым разом
     **/
    cmd_checkChanges:function() {
    
        // Проверяем изменение значения
        var val = this.info("value");
        if(val!=this.private_lastValue) {
            this.fire("change",val)
        }
        this.private_lastValue = val;
        
        // Проверяем изменение значения
        var val = this.info("textValue");
        if(val!=this.private_lastTextValue) {
            this.fire("textChange",val)
        }
        this.private_lastTextValue = val;
    },
    
    cmd_keepCaret:function() {
    
        if(inx.mouseLButton) {
            return;
        }
        var caret = inx.textfield.getCaret(this.input);
        if(caret) {
            this.caret = caret;
        }
    },
    
    cmd_setValue:function(val) {
    
        if(val===undefined) {
            val = "";
        }
            
        if(this.info("value")+""==val+"") {
            return;
        }
        
        this.private_value = val+"";
            
        this.cmd("setTextValue",val);
        
        this.cmd("checkChanges");
        
    },
    
    info_value:function() {
        if(this.input) {
            return this.info("textValue");
        }
        return this.private_value;
    },
    
    
    /**
     * Изменяет текстовое значение поля
     * Если input еще не создан, не выполнит никаких действий
     **/
    cmd_setTextValue:function(val) {
    
        if(this.input && this.input.get(0)) {
        
            if(val===undefined) {
                val = "";
            }
        
            this.input.get(0).value = val;
            this.cmd("checkChanges");
        }
        
    },

    /**
     * Возвращает текстовое значение поля
     **/
    info_textValue:function() {
        var input = this.input;
        if(input) {
            ret = input.val()+"";
            ret = ret.replace(/\r\n/g, "\n");
            return ret;
        } 
        
        return "";
        
    },
    
    info_calculatedTextValue:function() {
        return this.info("value");
    },
    
    cmd_keydown:function(e) {
    
        // Позволяем всплыть некотороым кодам клавишь, типа Esc
        if(e.keyCode==27) {
            return;
        }
        
        if(e.keyCode==13) {
            this.bubble("submit");
        }
            
        return "stop";
    },
    
    /** 
     * Выделяет весь текст в текстовом поле
     **/
    cmd_select:function() {
        if(this.input) {
            this.input.select();
        }
        this.private_select = true;
    },
    
    cmd_destroy:function() {
        if(this.buttons)
        for(var i in this.buttons)
            this.buttons[i].cmd("destroy");
        this.base();
    },
    
    info_caret:function() {
        return this.caret;
    },
    
    cmd_setCaret:function(start,end) {
        inx.textfield.setCaret(this.input,start,end);
        this.cmd("keepCaret");
    },
    
    cmd_syncLayout:function() {
    
        this.base();
        
        // Выравниваем кнопки справа
        if(this.buttons) {
            for(var i in this.buttons) {
                this.buttons[i].data("el").css({
                    left:this.info("width")-23*(i+1)
                });
            }
        }
        
        var h = this.info("innerHeight");
        
        if(this.input && this.input.get(0).nodeName=="INPUT") {
            this.input.css({
                height:h,
                fontSize:h-8
            });
        }
        
    },
    
    cmd_replaceSelection:function(prefix,suffix) {
    
        if(prefix===undefined)
            prefix = "";

        if(suffix===undefined)
            suffix = "";
    
        var src = this.info("value");
        
        var caret = this.info("caret");
        var a = src.substr(0,caret.start);
        var b = src.substr(caret.start,caret.end-caret.start);
        var c = src.substr(caret.end,src.length-caret.end);
        this.cmd("setValue",a+prefix+b+suffix+c);
        this.cmd("setCaret",(a+prefix).length,(a+prefix+b).length);
    }

});

inx.textfield.focus = function() {
    inx.cmp.fromElement(this).task("focus");    
}
inx.textfield.blur = function() {
    inx.cmp.fromElement(this).task("blur");    
}