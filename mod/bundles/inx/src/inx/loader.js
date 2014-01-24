// @link_with_parent

inx.loader = {

    heap:[],
    code:[],
    count:0,
    handlers:[],
    dependency:[],
    ready:{},
    
    is_requested:function(name) {
        for(var i=0;i<this.heap.length;i++)
            if(this.heap[i]==name)
                return true;
        return false;
    },    

    load:function(name,handler) {
    
        if(handler) inx.loader.handlers.push(handler);
    
        // Определяем путь
        var path = name.split(".");
        path = inx.conf.url+path.join("/")+".js";

        if(inx.loader.is_requested(name)) return;

        inx.loader.heap.unshift(name);
        inx.loader.count++;        
    
        $.ajax({
            type: "GET",
            url: path,
            cache:true,
            dataType:"html",
            success:function(data){
            
                var include = (data.split("\n")[0]+"").match(/\/\/[ ]*@include(.*)/);
                include = include ? include[1].split(",") : [];
                for(var i in include)
                    inx.loader.load($.trim(include[i]));
                inx.loader.count--;
                inx.loader.code[name] = data;

                inx.loader.dependency[name] = include;
                
                if(inx.loader.count==0)
                    inx.loader.exec();
            },
            error:function() {
                inx.msg("Script loading error ("+name+")",1)
            }
        });        
    },
    
    exec:function() {
    
        // Дублируем массив обработчиков
        // Т.к. в процесе выполнения могут быть объявлены новые обработчики,
        // то они должны быть добавлены в новый массив
        var handlers = [];
        for(var i=0;i<inx.loader.handlers.length;i++)
            handlers.push(inx.loader.handlers[i]);
        this.handlers = [];
        
        for(var i=0;i<inx.loader.heap.length;i++)
            inx.loader.eval(inx.loader.heap[i]);
        for(var i=0;i<handlers.length;i++)
            inx(handlers[i]).cmd("handleLoad");
    },
    
    eval:function(name) {

        name = $.trim(name);
        if(!inx.loader.code[name]) return;

        for(var i=0;i<inx.loader.dependency[name].length;i++)
            inx.loader.eval(inx.loader.dependency[name][i]);

        try {
            eval(inx.loader.code[name]);
        }
        catch(e) {
            inx.msg("error while eval "+name,1);
            inx.msg(e,1);
        }
        inx.loader.code[name] = null;
        inx.loader.ready[name] = true;
    },
    
    debug:function() {
        var r = [];
        for(var i in inx.loader.ready)
            r.push(i);
        return r.join("<br/>");
    }
}
