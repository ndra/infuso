// @link_with_parent

inx.dialog.manager = new function() {

    var stack = [];

    this.register = function(dlg) {
        dlg = inx(dlg).id();
        stack.push(dlg);
    }
    
    this.unregister = function(dlg) {
    
        // Функция удаления элемента из массива по его значению
        var removeA = function (arr) {
            var what, a = arguments, L = a.length, ax;
            while (L > 1 && arr.length) {
                what = a[--L];
                while ((ax= arr.indexOf(what)) !== -1) {
                    arr.splice(ax, 1);
                }
            }
            return arr;
        }
            
        dlg = inx(dlg).id();
        removeA(stack,dlg);
    }
    
    inx.hotkey("esc",function() {
    
        var dlg = stack[stack.length-1];
        if(!dlg) {
            return;
        }
        
        inx(dlg).cmd("handleDlgManagerEsc");
        
    })

}