// @link_with_parent
 
inx.css(
    ".inx-box{background:none;font-family:Arial, Helvetica, sans-serif;font-size:12px;position:relative;overflow:hidden;color:black;cursor:default;white-space:normal;}"
);

inx.box = inx.observable.extend({

    constructor:function(p) {
    
        if(!p.style) {
            p.style = {}
        }
        
        if(!p.defaultStyle) {
            p.defaultStyle = {}
        }
    
        // Рекомендованя ширина компонента, опираясь на внутрений размер 
        if(!p.private_widthContent) {
            this.private_widthContent = 1;
        }
        
        // Рекомендованя ширина компонента, опираясь на внешний размер       
        if(!p.private_widthParent) 
            this.private_widthParent = 3;
        
        // Рекомендованя высота компонента, опираясь на внутрений размер  
        if(!p.private_heightContent)      
            this.private_heightContent = 1;
        
        // Рекомендованя высота компонента, опираясь на внешний размер
        if(!p.private_heightParent)
            this.private_heightParent = 1;
        
        this.private_hidden = !!p.hidden;
        
        this.private_styleChangedKeys = {};
        
        if(p.style) {          
            for(var i in p.style) {
                this.style(i,p.style[i]);
            }
        }
                
        if(p.width && !p.style.width) {
            this.style("width",p.width)
        }
            
        if(p.height && !p.style.height) {
            this.style("height",p.height)
        }  
        
        this.base(p);
                
    },
    
    /**
     * Возвращает ось родителей компонента
     **/
    axis_parents:function() {
    
        var ret = [];
        var cmp = inx(this);
        while(cmp.exists()) {
            ret.push(cmp.id());
            cmp = cmp.owner();
        }
        return ret;
    
    },
        
    cmd_mousewheel:function(deltha,e) {
        this.owner().cmd("mousewheel",deltha,e)
    },
    
    info_component:function() {
        return this;
    },
    
    info_type:function() {
        return this.type;
    },
    
    owner:function() {
        return inx(this.id()).owner();
    },
    
    items:function() {
        return inx(this).items();
    },
    
    info_region:function() {
        return this.region;
    },
    
    info_resizable:function() {
        return this.resizable;
    },
    
    info_rendered:function() {
        return !!this.private_z74gi3f1in;
    },
    
    info_title:function() {
        return this.title;
    },
    
    cmd_setTitle:function(title) {
        if(this.title==title) {
            return;
        }
        this.title = title+"";
        this.fire("titleChanged",this.title);
    },
    
    info_name:function() {
        return this.name;
    },
    
    cmd_destroy:function() {    
        this.base();
        // Устраиваем геноцид )
        this.items().cmd("destroy");
        // Удаляем себя из родителя )
        this.owner().cmd("remove",this);
        // Удаляем контейнер
        $(this.el).remove();
    },
    
    cmd_render:function() { 
    
        for(var i in this.defaultStyle) {
            if(this.private_style[i]===undefined) {
                this.style(i,this.defaultStyle[i]);
            }
        }
    
        this.el = $("<div class='inx-box' >");
        this.el.data("id",this.id());

        if(this.id()==inx.focusManager.cmp().id()) {
            this.cmd("handleFocusChange",true);
        }
        
        if(this.private_hidden) {
            this.cmd("hide");
        }    
        
        this.task("completeRender");
    },
    
    cmd_completeRender:function() {
        this.fire("render");
        this.cmd("updateStyle");
        this.private_layoutReady = true;
        inx.service("boxManager").outerSizeChanged(this.id());
    },
    
    info_layoutReady:function() {
    
        if(this.style("width")=="parent" && !this.private_widthParentSet) {
            return false;
        }
    
        return !!this.private_layoutReady;
    },
    
    cmd_appendTo:function(container) {
    
        if(!this.el) {
           return;
        }
           
        if(container) {
            this.container = $(container);
        }
            
        this.el.appendTo(this.container);
        this.task("syncLayout");

    },
    
    style:function(key,val) {       
  
        // Возврат результата
        if(val===undefined) {        
            var ret = this.private_style[key];
            if(ret===undefined) {
                return inx.box.defaultValues[key];     
            }
            return ret;
        }                
        
        // Запись результата
        var s = this.style(key);        
        if(s!=val) {
            this.private_style[key] = val; 
            this.private_styleChangedKeys[key] = true;
            this.cmd("clearInfoBuffer");
            this.task("updateStyle");
            inx.service("boxManager").innerSizeChanged(this.id());
        }        
        return this;
    },
    
    cmd_updateStyle:function() {
    
        if(!this.info("rendered")) {
            return;
        }
    
        for(var key in this.private_styleChangedKeys) {
        
            var val = this.private_style[key];
            switch(key) {

                case "background":
                    this.el.css("background",this.private_style["background"]);
                    break;
                    
                case "color":
                    this.el.css("color",this.private_style["color"]);
                    break;
                    
                case "borderRadius":
                    this.el.css("borderRadius",this.private_style["borderRadius"]);
                    break;
                    
                case "border":
                    this.el.css("border",(this.private_style["border"] ? 1 : 0 )+"px solid #cccccc");
                    this.cmd("updateBox");
                    break;
                    
                case "shadow":
                    this.el.css("box-shadow",this.private_style["shadow"] ? "0 0 10px rgba(0,0,0,.2)" : "none" );
                    break;
                    
                case "padding":
                case "spacing":
                case "maxHeight":
                case "vscroll":
                case "hscroll":
                case "autoWidth":
                case "titleMargin":
                case "valign":
                case "break":
                case "iconWidth":
                case "iconHeight":
                case "fontSize":
                case "iconAlign":
                    this.task("syncLayout");
                    break;                    
                    
                case "width":
                    this.cmd("updateBoxWidth");
                    break;
                    
                case "height":
                    this.cmd("updateBoxHeight");                    
                    break;
                
                default:
                    inx.msg("Unknown style "+key,1);
                    break;
            }
        }
        this.private_styleChangedKeys = {};
    },

    info_container:function() {
        return this.container;
    },
    
    /**
     * Устанавливает ширину панели (по родительскому компоненту)
     * Ширина расчитывается с учетом рамки
     **/
    cmd_width:function(width) {    
    
        width = parseInt(width);
        if(width<0) {
            width = 1;
        }
        
        this.private_widthParentSet = true;
            
        if(this.private_widthParent == width) {
            return;
        }
        
        this.private_widthParent = width;
        
        if(this.style("width")=="parent") {
            this.cmd("clearInfoBuffer");
            this.task("updateBoxWidth");   
            inx.service("boxManager").outerSizeChanged(this.id());
        }
    },
    
    /**
     * Устанавливает ширину панели.
     * Ширина расчитывается с учетом рамки
     **/
    cmd_widthContent:function(width) {    
    
        if(this.private_widthContent == width) {
            return;
        }
    
        this.cmd("clearInfoBuffer");
    
        this.private_widthContent = width;
        this.task("updateBoxWidth");   
        inx.service("boxManager").outerSizeChanged(this.id());
    },
    
    /**
     * Возвращает ширину панели с учетом рамки или 0 если панель скрыта.
     **/
    info_width:function() {
    
        if(this.private_hidden) {
            return 0;
        }
            
        var width = this.style("width");
        
        if(width=="parent") {
            width = this.private_widthParent;            
        }

        if(width=="content") {
            width = this.private_widthContent;
        }
                    
        return width*1;
    },
    
    cmd_updateBoxWidth:function() {
    
        var b = this.private_style.border ? 2 : 0;
        var width = this.info("width") - b;
        var hash = width + ":" + b;
        
        if(this.private_boxHashX!=hash) {
            if(this.el) {            
                this.el.css("width",width);                
            }
        }
        
        this.private_boxHashX = hash;
    },
    
    cmd_updateBoxHeight:function() {
    
        var b = this.private_style.border ? 2 : 0;
        var height = this.info("height") - b;
        var hash = height + ":" + b;
        if(this.private_boxHashY!=hash) {        
            if(this.el) {            
                this.el.css("height",height);  
                
                // Важно! размер бокса может измениться даже без изменения параметров компонента
                // К примеру, если доабвить панель в растягивающуюся панель, ее размер умеличится,
                // хотя никакой команды на смену размера не было
                // Поэтому нужна доп. проверка при изменении реальных размеров компонента              
                inx.service("boxManager").outerSizeChanged(this.id());
            }            
        }
        
        this.private_boxHashY = hash;
    },
    
    /**
     * Перерисовывает прямоугольник компонента: применяет ширину, высоту и бордер
     **/
    cmd_updateBox:function() {    
        this.cmd("updateBoxWidth");
        this.cmd("updateBoxHeight");
    },
    
    info_resizable:function() {
        return !!this.resizable
    },

    /**
     * Устанавливает высоту панели в режиме "По родителю"
     **/
    cmd_height:function(height) {  
    
        if(this.private_heightParent == height) {
            return;
        }
        
        this.cmd("clearInfoBuffer");
      
        this.private_heightParent = height;
        this.task("updateBoxHeight");
        inx.service("boxManager").outerSizeChanged(this.id());
    },
    
    /**
     * Устанавливает высоту панели в режиме "По контенту"
     **/
    cmd_setContentHeight:function(height) {

        if(this.private_heightContent == height) {
            return;
        }
        
        this.cmd("clearInfoBuffer");
        
        this.private_heightContent = height;
        this.task("updateBoxHeight");
        inx.service("boxManager").outerSizeChanged(this.id());
    },
    
    /**
     * Возвращает высоту контента
     **/         
    info_contentHeight:function() {
    
        if(this.private_heightContent instanceof Function) {
            this.private_heightContent = this.private_heightContent();    
        }
    
        return this.private_heightContent;
    },

    /**
     * Возвращает реальную высоту компонента
     **/
    info_height:function() {
    
        if(this.private_hidden) {
            return 0;
        }
            
        var height = this.style("height");
        
        if(height=="parent") {
            height = this.private_heightParent;            
        }

        if(height=="content") {
        
            height = this.info("contentHeight");
            
            height+= this.style("padding")*2;
            height+= this.private_style.border ? 2 : 0;
            height+= this.info("sideHeight") || 0;
            
            var maxh = this.style("maxHeight");
            if(maxh && height>maxh) {
                height = maxh;     
            }   
            
        }
                    
        return height*1;
    },

    cmd_border:function(b) {
        this.style("border",b);
    }, 
    
    info_border:function() {
        return this.style("border");
    },

    /**
     * Возвращает ширину панели без учета бордера
     **/
    info_innerWidth:function() {
        return this.info("width") - (this.private_style.border ? 2 : 0)
    },
    
    /**
     * Возвращает высоту панели без учета бордера
     **/
    info_innerHeight:function() {
        return this.info("height") - (this.private_style.border ? 2 : 0)
    },

    cmd_handleFocusChange:function(flag) {
        flag = !!flag;
        if(!this.el) return;
        this.el.css("borderColor",flag?"blue":"#cccccc");
        this.fire(flag?"focus":"blur");
        flag ? this.el.addClass("inx-focused") : this.el.removeClass("inx-focused")
    },
    
    cmd_handleSmoothBlur:function() {
        this.fire("smoothBlur");
    },

    /**
     * Устанавливает фокус на компонент
     **/
    cmd_focus:function() {

        // Если компонент не виден, не фокусируемся на нем    
        if(!this.info("visibleRecursive")) {
            return;
        }
    
        inx.focusManager.focus(this.id());
    },
    
    cmd_blur:function() {
        inx.focusManager.blur(this.id());
    },
    
    cmd_click:function(e) {
        this.fire("click",e);
    },

    cmd_dblclick:function(e) {
        this.fire("dblclick",e);
    },
    
    cmd_mousedown:function(e) {
        this.fire("mousedown",e);
    },
    
    cmd_mouseup:function(e) {
        this.fire("mouseup",e);
    },
    
    fire_key:function(e) {
        this.fire("keydown",e);
        return true;
    },

    /**
     * Показывает компонент
     **/    
    cmd_show:function() {

        if(this.el) {
            this.el.css("display","block");
        }
        
        this.cmd("clearInfoBuffer");
            
        this.fire("show");

        this.private_hidden = false;
        this.task("updateBox");
        inx.service("boxManager").outerSizeChanged(this.id());
        
    },
    
    cmd_hide:function() {
    
        if(this.el) {
            this.el.css("display","none");
        }
        
        this.cmd("clearInfoBuffer");
        this.fire("hide");
        this.private_hidden = true;
        inx.service("boxManager").outerSizeChanged(this.id());
        
    },
    
    cmd_toggle:function() {
        if(this.info("hidden")) {
            this.cmd("show");
        } else {
            this.cmd("hide");
        }
    },
    
    // true если компонент скрыт
    // false если компонент видимый
    info_hidden:function() {
        return !!this.private_hidden;
    },
    
    info_visible:function() {    
        return !this.private_hidden;
    },
    
    // Рекурсивно проверяет виден ли объект
    info_visibleRecursive:function() {
    
        // Если сам объект спрятан, возвращаем false
        if(this.info("hidden")) {
            return false;
        }
            
        visible = true;    
        inx(this).owners().each(function() {
            if(this.info("hidden"))
                visible = false;
        });
        return visible;
    },
    
    info_param:function(key) {
        return this[key];
    },
    
    cmd_syncLayout:function() {

    },
        
    info_help:function() {
        return this.help;
    },
   
    cmd_heightContentRaw:function(height) {     
        this.cmd("heightContent",height);
        this.cmd("clearInfoBuffer");
    },
    
    cmd_nativeUpdateLoader:function() {
        if(!inx.conf.ajaxIndicator) return false;
        var n = inx(this.id()).data("currentRequests");
        if(!n) {
            $(this.privateLoaderEl).remove();
            this.privateLoaderEl = null;
        }
        else {
            if(!this.privateLoaderEl) {
                this.privateLoaderEl = $("<div>").css({background:"white",position:"absolute",padding:5,zIndex:100});
                $("<img>").prop("src",inx.img("loader")).appendTo(this.privateLoaderEl)
            }
            this.privateLoaderEl.appendTo(this.el)
            if(this.__body) {
                var pos = this.__body.position();
                this.privateLoaderEl.css({top:pos.top,left:pos.left});
            }
        }
    }
        
}); 
inx.box.defaultValues = {
    width:"parent",
    height:"content",
    sidePriority:"v",
    padding:0,
    spacing:0,
    titleMargin:0,
    valign:"center",
    autoWidth:true,
    iconWidth:16,
    iconHeight:16,
    fontSize:12,
    iconAlign:"left",
    borderRadius:0,
    shadow:0,
    textColor:"black",
    vscroll:false,
    hscroll:false
}
