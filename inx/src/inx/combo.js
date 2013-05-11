// @include inx.textfield

inx.combo = inx.textfield.extend({

    constructor:function(p) {
    
        p.buttons = [{
            icon:"trigger",
            onclick:[this.id(),"expand"]
        }];
        
        this.private_textValueForId = {};
        
        this.base(p);
        
        this.on("textChange","handleChangesNative");
        this.on("blur","handleBlur");
        
        var cmp = this;
        this.on("render",function() {
            this.suspendEvents();
            cmp.cmd("setTextValue",p.text);
            this.unsuspendEvents();
        });

    },

    cmd_syncLayout:function() {
        this.private_popup && this.private_popup.style("width",this.info("width")-10);
        this.base();
    },

    /**
     * Создает выпадающий список
     **/    
    cmd_createList:function() {
    
        if(this.private_list) {
            return;
        }
            
        // Список
        this.private_list = inx({
            type:"inx.list",
            data:this.data,
            loader:this.loader,
            style:{
                maxHeight:200,
                height:"content",
                border:0,
            },listeners:{
                itemclick:[this.id(),"native_handleSelect"],
                load:[this.id(),"handleLoad"],
                beforeload:[this.id(),"beforeLoadNative"]
            }
        });
        
        // Диалог для писка
        this.private_popup = inx({
            type:"inx.dialog",
            style:{
                background:"none",
                width:200,
                border:0
            },showTitle:false,            
            modal:false,
            clipToOwner:true,
            items:[this.private_list]
        }).cmd("hide").cmd("render").setOwner(this);
        
    },

    cmd_destroy:function() {
        inx(this.private_popup).cmd("destroy");
        this.base();
    },

    cmd_beforeLoadNative:function(loader) {
        loader.search = this.info("textValue");
        this.fire("beforeload",loader);
    },

    cmd_handleLoad:function() {
    
        if(this.info("textValue")!="") {
    
            var sel = this.list().info("selection")[0];
            
            if(!sel) {
                this.task("selectFirst");
            }
        
        }

        
        if(inx.focusManager.cmp().id()!=this.id()) {
            this.task("updateCalculatedValue");
        }
    },
    
    cmd_selectFirst:function() {
        this.list().cmd("setPosition",0);    
    },
    
    cmd_handleBlur:function() {
        this.cmd("updateCalculatedValue");
    },

    list:function() {
        return this.private_list || inx();
    },
        
    axis_list:function(){
        return this.list();
    },

    cmd_native_handleSelect:function(id) {
        this.cmd("setValue",id);
        this.cmd("collapse");
        this.task("focus");
    },
    
    info_calculatedTextValue:function() {
    
        if(!this.private_calculatedValue) {
        
            var item = this.list().info("item",this.private_value);
            if(item) {
                this.private_calculatedValue = item.data.text;
                            
            }
        
        }
        return this.private_calculatedValue;
    },
    
    cmd_updateCalculatedValue:function() {
        var txt = this.info("calculatedTextValue");
        if(txt) {
            this.cmd("setTextValue",txt);
        }
    },

    cmd_setValue:function(val) {    
    
        if(val==this.private_value) {
            return;
        }
        
        this.private_value = val;
        this.private_calculatedValue = null;
        
        this.cmd("updateCalculatedValue");
        
    },
    
    info_value:function() {
        return this.private_value;
    },
    
    cmd_handleChangesNative:function() {
    
        this.cmd("expand");
        this.list().task("load");
        
        if(this.info("textValue")=="") {
            this.private_value = false;
        }
    },

    cmd_handleSmoothBlur:function() {
        this.cmd("collapse");
    },

    /**
     * Раскрывает список
     **/
    cmd_expand:function() {
        this.cmd("createList");
        this.private_popup.cmd("show");
        this.expanded = true;
    },
    
    /**
     * Свораивает список
     **/
    cmd_collapse:function() {
        inx(this.private_popup).cmd("hide");
        this.expanded = false;
    },

    /**
     * Раскрыт ли список
     **/
    info_expanded:function() {
        return this.expanded;
    },

    cmd_keydown:function(e) {
    
       switch(e.which) {
       
            // Enter
            case 13:
                if(this.info("expanded")) {
                    this.cmd("setValue",this.list().info("selection")[0]);
                    this.cmd("collapse");
                } else {
                    this.base(e);
                }
                return false;
                break;
                
            // Клавиши вверх-вниз    
            case 38:
                this.cmd("expand");
                this.list().cmd("selectUp");
                e.preventDefault();
                return false;
                
            case 40:
                this.cmd("expand");
                this.list().cmd("selectDown");
                e.preventDefault();
                return false;
            
            default:
                return this.base(e);
                break;
        }
    }

})
