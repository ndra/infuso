// Менюшка ------------------------------------------------------------------------------

var _over = 0;
$(document).mousedown(function(e){

    var over = $(e.target).parents().andSelf().is(!_over ? "#raub2v07e-title" : "#raub2v07e-menu");
    
    if(over!=_over)
    if(over) {
        $("#raub2v07e-menu").css("display","block");
        $("#raub2v07e-menu").stop().animate({opacity:1},"fast")
        _over = over;
    }
    else {
        $("#raub2v07e-menu").stop().animate({opacity:0},"fast",null,function(){
            $("#raub2v07e-menu").css("display","none");
        })
        _over = over;
    }
});