// @link_with_parent

inx.hotkey = {}

inx.hotkey = function(key,handler) {

    if(handler instanceof Array) {
    
        handler.push({
            visibleOnly:true
        });
    
    }

    inx.service("key").on(key,handler);
}

