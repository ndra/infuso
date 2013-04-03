inx.ns("inx.debug").cookie = function(name, value, options) {
    if (typeof value != 'undefined') { // name and value given, set cookie
        options = options || {};
        if (value === null) {
            value = '';
            options.expires = -1;
        }
        var expires = '';
        if (options.expires && (typeof options.expires == 'number' || options.expires.toUTCString)) {
            var date;
            if (typeof options.expires == 'number') {
                date = new Date();
                date.setTime(date.getTime() + (options.expires * 24 * 60 * 60 * 1000));
            } else {
                date = options.expires;
            }
            expires = '; expires=' + date.toUTCString(); // use expires attribute, max-age is not supported by IE
        }
        // CAUTION: Needed to parenthesize options.path and options.domain
        // in the following expressions, otherwise they evaluate to undefined
        // in the packed version for some reason...
        var path = options.path ? '; path=' + (options.path) : '';
        var domain = options.domain ? '; domain=' + (options.domain) : '';
        var secure = options.secure ? '; secure' : '';
        document.cookie = [name, '=', encodeURIComponent(value), expires, path, domain, secure].join('');
    } else { // only name given, get cookie
        var cookieValue = null;
        if (document.cookie && document.cookie != '') {
            var cookies = document.cookie.split(';');
            for (var i = 0; i < cookies.length; i++) {
                var cookie = jQuery.trim(cookies[i]);
                // Does this cookie string begin with the name we want?
                if (cookie.substring(0, name.length + 1) == (name + '=')) {
                    cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
                    break;
                }
            }
        }
        return cookieValue;
    }
};

// -----------------------------------------

$(function(){

    var left = inx.debug.cookie("inx.debug.left") || 0;
    var top = inx.debug.cookie("inx.debug.top") || 0;
    
    var el = $("<div>").css({
        border:"1px solid red",
        zIndex:10000,
        position:"absolute",
        padding:5,
        fontSize:10,
        background:"white",
        left:0,
        top:0,
        maxHeight:400,
        overflowY:"scroll",
        cursor:"move"
    }).appendTo("body");

    el.css("top",top*1);
    el.css("left",left*1);
    
    var that = this;
    el.mousedown(function(e){
        that.dd = true;
        that.sx = e.clientX;
        that.sy = e.clientY;
    });

    
    $(document).mouseup(function(){ that.dd = false });
    $(document).mousemove(function(e){
        if(!that.dd) return;
        var x = (that.sx - e.clientX) || 0;
        el.css({left:parseInt(el.css("left"))-x});
        that.sx = e.clientX;
        var y = (that.sy - e.clientY) || 0;
        el.css({top:parseInt(el.css("top"))-y});
        that.sy = e.clientY;        
        e.preventDefault();        
        inx.debug.cookie("inx.debug.left",el.offset().left);
        inx.debug.cookie("inx.debug.top",el.offset().top);        
    });
    
    // Создаем тулбар и контейнеры
    var te = $("<div>").appendTo(el);
    var toolbar = [];
    toolbar.push($("<span>").css({marginRight:10}).html("main").appendTo(te));
    toolbar.push($("<span>").css({marginRight:10}).html("loaded").appendTo(te));
    var content = $("<div>").appendTo(el);
    
    var selected = 0;
        
    var update = function() {
        content.html("");
        switch(selected*1) {
            case 0:   
                $("<div>").html("Command executed: "+inx.observable.debug.cmd).appendTo(content);
                $("<div>").html(inx.box.manager.debug()+"").appendTo(content);
                for(var i in inx.cmp.buffer) {
                    var a = $("<div>").appendTo(content);
                    var obj = inx.cmp.buffer[i].obj;
                    var n = inx.observable.debug.cmdCountByID[i];
                    a.html(i+":"+obj.type+":"+n);
                    
                    if(1)
                        for(var j in inx.observable.debug.cmds[i])
                            $("<div>").css({marginLeft:10,color:"gray"}).appendTo(content).html(j+":"+inx.observable.debug.cmds[i][j]);
                    
                    if(i==inx.focusManager.cmp().id())
                        a.css({border:"1px solid blue"});
                    var debug = inx(i).info("debug");
                    if(debug)
                        $("<div>").css({marginLeft:10,color:"gray"}).appendTo(content).html(debug+"");
                        
                }        
                break;
            case 1:
                content.html(inx.loader.debug()+"");
                break;
            
        }        
    }
    
    var select = function(id) {
        for(var i in toolbar)
            $(toolbar[i]).css({background:"none"});
        $(toolbar[id]).css({background:"#cccccc"});
        selected = id;
        update();
    }
    for(var i in toolbar) {
        toolbar[i].data("id",i);
        toolbar[i].click(function(){ select($(this).data("id")) })
    }
    select(0);
    
    setInterval(update,1000);
    $(document).keydown(function(ev){
        if(ev.keyCode==192) {
            if(el.css("display")=="block")
                el.hide();
            else
                el.show();
        }
    });

})