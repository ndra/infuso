// @link_with_parent

// Загружает тини и вызывает функцию как только тини будет готов
inx.mod.TinyMCE.editor.loader = new function(){

    this.fns = [],

    this.load = function(fn) {
    
        // Если тини готов, выполняем fn
        if(this.ready) {
            fn();
        }
        // Если не готов, но запрошен, добавляем fn в список
        else if(this.included) {
            this.fns.push(fn);
        }
        
        else {
            this.fns.push(fn);
            this.included = true;
            this.includeTinyMCE();           
        }
    },
    
    this.includeTinyMCE = function() {
    
        // Криворукие моксикоды
        $("<div></div>").appendTo("body").get(0).id = "__ie_onload";
        var script = document.createElement("script");
        script.src = "/TinyMCE/editor/tiny_mce.js";
        script.onload = inx.delegate(this.onload,this);
                
        var t = this;
        script.onreadystatechange = function(e){
            if(script.readyState=="complete" || script.readyState=="loaded")
                t.onload();
        };
        document.body.appendChild(script);
    },
    
    this.onload = function() {
        this.ready = true;
        tinymce.dom.Event.domLoaded = true;
        for(var i=0;i<this.fns.length;i++)
            this.fns[i]();
        this.fns = [];
    }
    
};
