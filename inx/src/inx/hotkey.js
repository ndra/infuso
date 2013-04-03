// @link_with_parent

inx.hotkeyManager = new function() {

    /*this.init = function() {
        var that = this;
        setInterval(function(){that.collectIFrames(window,Math.random())},1000);
        this.handlers = {};
    }

    // Проходится по всем ифреймам и вешает обработчики на клавиши
    this.collectIFrames = function(wnd,collect_id) {
    
        try {
        
            if(!wnd)
                return;
            if(!wnd.document)
                return;
            if($(wnd.document).data("inx.hotkey.collect_id")==collect_id)
                return;
                
            $(wnd.document).data("inx.hotkey.collect_id",collect_id);

            var t = this;

            var ifr = $(wnd.document.body).find("iframe");
            
            // Находим все ифреймы в данном окне
            // Запускаем функцию рекурсивно для этих ифреймов
            ifr.each(function(){    
           
                    t.collectIFrames(this.contentWindow,collect_id);
                
            });

            // Вешаем обработчики
            if(!$(wnd.document).data("inx.hotkey")) {
                $(wnd.document).data("inx.hotkey",true);
                $(wnd.document).keydown(function(e){t.handleKeyDown(e)});
                $(wnd.document).keypress(function(e){t.handleKeyDown(e)});
                $(wnd.document).keyup(function(e){t.handleKeyUp(e)});
            }
            
        } catch(e) {}
    }

    this.pressed = {};

    this.handleKeyUp = function(e) {
        this.pressed[e.keyCode] = 0;
    }*/

    // Вызывается при нажатии клавиши
    /*inx.service("key").on("keydown", function(p) {
    
        //inx.msg(12);

        var hash = "c:"+(p.ctrlKey?"1":"0")+"-s:"+(p.shiftKey?"1":"0")+"-key:"+p.keyCode;
        var handlers = this.handlers[hash];
        if(!handlers) {
            return;
        }

        for(var i=0;i<handlers.length;i++) {

            var obj = inx(handlers[i].obj);

            // Убираем запросы к удаленным объектам
            if(!obj.exists()) {
                handlers.splice(i,1);
                i--;
                continue;
            }

            // Если объект виден и событие - keydown - выполняем обработчик
            if(obj.info("visibleRecursive")) {
            
                var ret = obj.cmd(handlers[i].fn);
                if(ret===false) {
                    p.preventDefault();
                }                
            }
            
        }
        
    }); */

    this.on = function(p,obj,fn) {
    
        obj = inx(obj).id();
        var keys = {
            esc:27,
            enter:13,
            f1:112,
            f2:113,
            f3:114,
            f4:115,
            f5:116,
            tab:9
        }
        s =(p+"").split("+");
        p = {};
        for(var i=0;i<s.length;i++) {
            var part = s[i];
            if(part==parseInt(part)) p.keyCode=part;
            else if(part=="ctrl") p.ctrlKey = true;
            else if(part=="shift") p.ctrlKey = true;
            else p.keyCode = keys[part] || part.toUpperCase().charCodeAt();
        }

        var hash = "c:"+(p.ctrlKey?"1":"0")+"-s:"+(p.shiftKey?"1":"0")+"-key:"+p.keyCode;
        if(!this.handlers[hash]) this.handlers[hash] = [];
        this.handlers[hash].push({obj:obj,fn:fn});
    }

    //this.init();
}

inx.hotkey = function(a,b,c) { inx.hotkeyManager.on(a,b,c); }
inx.hotkey.is = function(code) { return !!inx.hotkeyManager.pressed[code]; }
