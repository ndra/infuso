// @link_with_parent
inx.css(
    ".inx-msg-container{word-break:break-word;top:20px;position:fixed;font-family:Arial;z-index:100001000;}",
    ".inx-msg{width:300px;background:gray;box-shadow:0px 0px 20px rgba(0,0,0,.3);color:white;padding:4px 8px;margin-bottom:2px;}",
    ".inx-msg-error{background:red;}"
);

inx.msg = function(text,error,adv) {

    text+="";

    if(!inx.msg.log) inx.msg.log = [];
    inx.msg.log.push({text:text,error:error});
    inx.msg.log = inx.msg.log.splice(-30);

    if(!inx.msg.__container)
        inx.msg.__container = $("<div class='inx-msg-container' />").prependTo("body");
    inx.msg.updateContainerPosition();

    if(typeof(text)=="object") {
        var str = "";
        for(var i in text)
            str+=i+" : "+text[i]+"<br/>";
        text = str;
    }

    var msg = $("<div>")
        .addClass("inx-msg")
        .addClass("inx-roundcorners")
        .html(text+"");
    
    if(text.length<50) {
        msg.css({
            fontSize:18
        });
    }
    
    error && msg.addClass("inx-msg-error");
    msg.css("opacity",0);

    msg.appendTo(inx.msg.__container);
    
    var max = 15;
    var n = inx.msg.__container.children().length;
    if(n>max) {
        inx.msg.__container.children().first().remove();
    }
    
    msg.animate({opacity:1},500)
        .animate({opacity:1},2000)
        .animate({opacity:0},"slow")
        .hide("slow");
}

inx.msg.updateContainerPosition = function() {

    if(!inx.msg.__container) {
        return;
    }

    if(inx.msg.mouseX < $("body").width() - 300 - 30) {
        var left = $("body").width()-330;
    } else {
        var left = 30;
    }
    inx.msg.__container.css("left",left);
}

$(document).mousemove(function(event) {
    inx.msg.mouseX = event.clientX;
    inx.msg.updateContainerPosition();
})


