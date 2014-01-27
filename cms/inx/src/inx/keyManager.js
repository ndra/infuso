// @link_with_parent

$(function() {
    setTimeout(function() {
    
        // Устанавливаем обработчик keydown
        inx.service("key").on("keydown",function(e) {

            var cmp = inx.focusManager.cmp();
            while(cmp.exists()) {
                var ret = cmp.cmd("keydown",e);
                if(ret===false) {
                    e.preventDefault();
                    break;
                } else if(ret=="stop") {
                    break;
                }
                cmp = cmp.owner();
            }

        });

        // Устанавливаем обработчик keypress
        inx.service("key").on("keypress",function(e) {

            var cmp = inx.focusManager.cmp();
            var str = String.fromCharCode(e.which);

            var ret = cmp.cmd("keypress",str);
            if(ret==false) {
                e.preventDefault();
            }

        });

    })
});