$(function() {
    $(".ufaa7h0-field").each(function(){
        var txt = $(this).prev();
        txt.mousedown(function() {var a = $(this); setTimeout(function() { a.next().focus() },0)});
        if($(this).val()) txt.hide();
        $(this).focus(function() { txt.hide() });
        $(this).blur(function() { if(!$(this).val()) txt.show() });
    })
});