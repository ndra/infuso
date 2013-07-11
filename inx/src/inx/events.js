// @link_with_parent

/**
 * Реализация подписки и вызова глобальных событий
 **/
inx.events = new function() {

    var that = this;
    
    var handlers = [];
    var globalHandlers = [];

    this.on = function(name,handler) {
        if(!handlers[name]) {
            handlers[name] = [];
        }
        handlers[name].push(handler);
    }
    
    this.global = function(handler) {
        globalHandlers.push(handler);
    }
    
    /**
     * Вызывает глобальное событие name
     **/
    this.fire = function(name,p1,p2,p3) {
        var h = handlers[name];
        that.processHandlersArray(globalHandlers,name,p1,p2,p3);
        return that.processHandlersArray(h,p1,p2,p3);                
    }
    
    /**
    * Выполняет массив коллбэков
    * Выбрасывает из него ссылки на уже не существующие объекты
    * Если хоть один из кэлбэков вернул false, возвращает false
    **/
    this.processHandlersArray = function(handlers,p1,p2,p3) {
    
        if(!handlers) {
            return;
        }
    
        var retFalse = false;
        var ret = null;        
        
        for(var i in handlers) {
        
            var handler = handlers[i];
            
            if(handler instanceof Function) {
            
                ret = handler(p1,p2,p3);
                retFalse = ret===false;
                
            } else {
            
                var cmp = inx(handler[0]);
 
                var extraParams = handler[2];
                if(extraParams) {
                    if(extraParams.visibleOnly && !cmp.info("visibleRecursive")) {
                        continue;    
                    }
                }
                
                ret = cmp.cmd(handler[1],p1,p2,p3);
                retFalse = ret===false;
            }
            
        }
        
        if(retFalse) {
            ret = false;
        }
        
        return ret;
    }
    
    inx.service.register("events",this);

}
