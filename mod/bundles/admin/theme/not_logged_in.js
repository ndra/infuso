$(function() {

    var ball = $("<div>")
        .css({
            width:10,
            height:10,
            background:"black",            
            opacity:.02,
            position:"absolute"
        }).appendTo("body");
    
    var x = 0;
    var y = 0;   
    var dx = 3;
    var dy = 3; 
    
    var width = $(window).width();
    var height = $(window).height();
    
    var tick = function() {
        x += dx;
        y += dy;
        ball.css({
            left:x,
            top:y
        })
        
        if(x<0) {
            dx*=-1;
        }
        
        if(x>width-20) {
            dx*=-1;
        }
        
        if(y<0) {
            dy*=-1;
        }
        
        if(y>height-20) {
            dy*=-1;
        }
        
    }
    
    setInterval(tick,20);

})