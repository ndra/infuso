// @link_with_parent

inx.key = new function() {

    var that = this;
    
    this.handleKeydown = function(e) {
    
        // Определяем нажата ли клавиша комманд
        if((inx.hotkeyManager.pressed[91] || inx.hotkeyManager.pressed[93]) && e.metaKey) {
            e.commandKey = true;
        }

        // Клавиша Command воспринимается системой как Control
        if(e.commandKey) {
            e.ctrlKey = true;
        }
       
        that.fire(e);
    
    }
    
    that.handleKeypress = function(e) {
        if(!e.ctrlKey) {
            e.char = String.fromCharCode(e.which);
            that.fire(e);
        }
    }
    
    this.init = function() {        
        $(document).keypress(that.handleKeypress);
        $(document).keydown(that.handleKeydown);
        inx.service.register("key",that);
    }
    
    this.handlers = {};
    
    this.on = function(event,handler) {
        if(!this.handlers[event]) {
            this.handlers[event] = [];
        }
        this.handlers[event].push(handler);
    }
    
    this.fire = function(event) {
    
        var type = event.type;
        var handlers = that.handlers[type];
        if(handlers) {
            for(var i in handlers) {
                handlers[i](event);
            }
        }
    
    }
    
    $(function() {
        that.init();
    })

}
