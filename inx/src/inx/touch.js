// @link_with_parent

inx.touch = new function() {

    // detectiong ios
    this.test = function() {
        var ua = navigator.userAgent;
        if(ua.match(/iPhone/i) || ua.match(/iPod/i) || ua.match(/iPad/i))
            return true;
        return false;
    }

    this.init = function() {

        if(this.test()) {
            inx.css(".inx-box{cursor:pointer;}");
        }
    }

    this.eventHandler = function(e) {

        switch(e.type) {

            case "touchstart":
                inx.touch.startX = event.touches[0].pageX;
                inx.touch.startY = event.touches[0].pageY;
                break;

            case "touchmove":
                if(event.touches.length==1) {

                    var x = event.touches[0].pageX - inx.touch.startX;
                    var y = event.touches[0].pageY - inx.touch.startY;
                    inx.touch.startX = event.touches[0].pageX;
                    inx.touch.startY = event.touches[0].pageY;

                    var cmp = inx.cmp.fromElement(e.target);

                    var params = {add:true,bubble:true};
                    cmp.cmd("scrollTop",-y,params);
                    cmp.cmd("scrollLeft",-x,params);

                    if(!params.xxx)
                        e.preventDefault();

                }
                break;

        }
    }

}

inx.touch.init();
$(document).bind("touchstart",inx.touch.eventHandler);
$(document).bind("touchmove",inx.touch.eventHandler);
