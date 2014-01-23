/**
 * Объект для работы с хэш-тэгом
 **/

inx.direct = {

    first:true,

    /**
     * Приватный метод. Вызывается по таймеру и проверяет изменения хэша
     **/
    check:function() {
    
        var h = (window.location.hash+"").substr(1).split("/");
        var hash = [];
        
        for(var i in h) {
            if(h[i]) {
                hash.push(h[i]);
            }
        }
        
        hash = hash.join("/");
        if(inx.direct.last!=hash) {
            inx.direct.handleChange(hash);
            inx.direct.last = hash;
        }
        
        inx.direct.first = false;
        
    },
    
    /**
     * Метод, реагирующий на изменения
     **/
    handleChange:function(h) {
    
        var segments = h.split("/");
        
        var params = {};
        var action = segments[0];
        for(var i=1;i<segments.length;i++) {
            if(i%2==0) {
                params[key] = segments[i];
            } else {
                key = segments[i];
            }
        }
        
        if(inx.direct.id) {
            inx(inx.direct.id).cmd(inx.direct.fn,{
                action:action,
                first:inx.direct.first,
                params:params,
                segments:segments,
                hash:h
            });
        }
    },
    
    /**
     * Возвращает текущий хэш
     **/
    get:function(n) {
        var h = (window.location.hash+"").substr(1);
        var segments = h.split("/");
        
        var params = {};
        var action = segments[0];
        for(var i=1;i<segments.length;i++) {
            if(i%2==0) {
                params[key] = segments[i];
            } else {
                key = segments[i];
            }
        }
        
        if(inx.direct.id) {
            return {
                action:action,
                params:params,
                segments:segments
            };
        }
    },
   
    /**
     * Устанавливает текущий хэш
     **/
    set:function() {
        var a = [];
        for(var i=0;i<arguments.length;i++) {
            a.push(arguments[i])
        }
        a = a.join("/");
        window.location.hash = a;
        this.check();
    },
    
    setAction:function(action,params) {
    
        var h = action;
        for(var i in params) {
            h += "/"+i+"/"+params[i]
        }
        window.location.hash = h;
    
    },
    
    /**
     * Устанавливает кэллбэк на изменение хэша
     **/
    bind:function(id,fn) {
        inx.direct.id = inx(id).id();
        inx.direct.fn = fn;
    }
}

setInterval(inx.direct.check,100);