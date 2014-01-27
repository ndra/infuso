// @link_with_parent
// @priority 100

inx.key = new function() {

    var that = this;

    this.handlers = {};
    this.pressed = {};
    
    this.handleKeydown = function(e) {
    
        inx.key.pressed[e.keyCode] = true;
    
        // Определяем нажата ли клавиша комманд
        if((inx.key.pressed[91] || inx.key.pressed[93]) && e.metaKey) {
            e.commandKey = true;
        }

        // Клавиша Command воспринимается системой как Control
        if(e.commandKey) {
            e.ctrlKey = true;
        }
       
        that.fire(e,"keydown");

        var ret = [];

        if(e.shiftKey) {
            ret.push("shift")
        }

        if(e.ctrlKey) {
            ret.push("ctrl");
        }

        ret.push(e.which);

        var name = ret.join("+");
        name = that.normalizeEventName(name);

        that.fire(e,name);
    
    }

    this.normalizeEventName = function(name) {

        var keyAliases = {
            esc:27,
            enter:13,
            f1:112,
            f2:113,
            f3:114,
            f4:115,
            f5:116,
            tab:9
        }

        var s =(name+"").split("+");
        p = {};
        for (var i = 0; i < s.length; i++) {
            var part = s[i];
            if(part==parseInt(part)) p.keyCode=part;
            else if(part=="ctrl") p.ctrlKey = true;
            else if(part=="shift") p.shiftKey = true;
            else p.keyCode = keyAliases[part] || part.toUpperCase().charCodeAt();
        }

        var ret = [];

        if(p.shiftKey) {
            ret.push("shift")
        }

        if(p.ctrlKey) {
            ret.push("ctrl");
        }

        ret.push(p.keyCode);

        return ret.join("+");
    }
    
    that.handleKeypress = function(e) {
        if(!e.ctrlKey) {
            e.char = String.fromCharCode(e.which);
            that.fire(e,"keypress");
        }
    }
    
    that.handleKeyUp = function(e) {
        inx.key.pressed[e.keyCode] = false;
    }
    
    this.init = function() {        
        $(document).keypress(that.handleKeypress);
        $(document).keydown(that.handleKeydown);
        $(document).keyup(that.handleKeyUp);
        inx.service.register("key",that);
    }
    
    /**
     * Устанавливает обработчик на нажатие клавиши
     **/
    this.on = function(event,handler) {

        switch(event) {
            case "keydown":
            case "keypress":
                break;
            default:
                event = inx.key.normalizeEventName(event);
                break;
        }

        inx.on("key/"+event,handler);

    }
    
    this.fire = function(event,type) {
        var ret = inx.fire("key/"+type,event);
        if(ret===false) {
            event.preventDefault();
        }
    }
    
    $(function() {
        that.init();
    })

}
