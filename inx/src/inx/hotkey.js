// @link_with_parent

inx.hotkey = {}

inx.hotkey = function(key,handler) {

    handler.push({
        visibleOnly:true
    });

    inx.service("key").on(key,handler);
}

