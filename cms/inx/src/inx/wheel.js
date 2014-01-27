// @link_with_parent

// ------------------------------------------------
inx.wheel = function(e) {

    if(!e)
        e = window.event;

    var delta = e.wheelDelta;    
    // Для FF    
    if(e.detail)
        delta = -e.detail*40;
    
    var cmp = inx.cmp.fromElement(e.target);
    
    if(cmp.cmd("mousewheel",delta)===false) {
        if(e.preventDefault)
            e.preventDefault();
        e.returnValue = false;
        if(e.stopPropagation)
            e.stopPropagation();
    }    
}
window.onmousewheel = document.onmousewheel = inx.wheel;
if(window.addEventListener)
    window.addEventListener('DOMMouseScroll', inx.wheel, false);