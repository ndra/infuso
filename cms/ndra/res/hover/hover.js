if(!window.ndra) window.ndra = {};

ndra.hover = function(what) {
    what = $(what);
    var img = what.children("img");
    var img1 = img.attr("src");
    var img2 = img.attr("ndra:img2");
    var w1 = img.width();
    var h1 = img.height();
    var w2 = img.attr("ndra:w2");
    var h2 = img.attr("ndra:h2");
        
    what.mouseenter(function() {
        img.attr("src",img2).css({zIndex:1002}).stop().animate({
            width:w2,
            height:h2,
            marginLeft:-(w2-w1)/2,
            marginTop:-(h2-h1)/2
        },"fast");
    });
    
    what.mouseleave(function() {
        img.attr("src",img1).css({zIndex:1}).stop().animate({
            width:w1,
            height:h1,
            marginLeft:0,
            marginTop:0
        },"fast");
    });
}

$(function() {
    $(".ndra-hover").each(function() {ndra.hover(this) });
});