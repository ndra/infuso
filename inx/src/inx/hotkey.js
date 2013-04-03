// @link_with_parent

inx.hotkey = {}

inx.hotkey = function(key,handler) {
    inx.service("key").on(key,handler);
}

