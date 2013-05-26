/**
 * Объект для работы с хэш-тэгом
 **/

inx.direct = {

    /**
     * Приватный метод. Вызывается по таймеру и проверяет изменения хэша
     **/
    check:function() {
        var h = (window.location.hash+"").substr(1).split("/");
        var hash = [];
        for(var i in h) if(h[i]) hash.push(h[i])
        hash = hash.join("/");
        if(inx.direct.last!=hash) {
            inx.direct.handleChange(hash);
            inx.direct.last = hash;
        }
    },
    
    /**
     * Метод, реагирующий на изменения
     **/
    handleChange:function(h) {
        var a = h.split("/");
        if(inx.direct.id) {
            inx(inx.direct.id).cmd(inx.direct.fn,a[0],a[1],a[2],a[3],a[4],a[5],a[6],a[7],a[8],a[9],a[10]);
        }
    },
    
    /**
     * Возвращает текущий хэш
     **/
    get:function(n) {
        var h = (window.location.hash+"").substr(1);
        var a = h.split("/");
        return a[n];
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
    
    /**
     * Устанавливает кэллбэк на изменение хэша
     **/
    bind:function(id,fn) {
        inx.direct.id = inx(id).id();
        inx.direct.fn = fn;
    }
}
setInterval(inx.direct.check,100);