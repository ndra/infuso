// @link_with_parent

inx.focusManager = new function() {

    var k = [8,9,13,33,34,35,36,37,38,39,40,45,46,27,20,18,16,17,19,91,93,112,113,114,115,116,117,118,119,120,121,122,123];
    this.systemKeys = {};
    for(var i=0;i<k.length;i++)
        this.systemKeys[k[i]] = true;

    this.focused = null;
    
    this.focus = function(id) {
        inx.focusManager.newFocus = id;
        if(!inx.focusManager.timeout)
            inx.focusManager.timeout = setTimeout(function() {inx.focusManager.applyFocus()});
    }

    this.blur = function(cmp) {
        if(this.focused==cmp)
            this.focus();
    }    
    
    this.applyFocus = function() {
        inx.focusManager.timeout = 0;
        var id = inx.focusManager.newFocus;
        if(inx.focusManager.focused==id) return;
        var last = inx.focusManager.focused;
        inx.focusManager.focused = id;
        inx(last).cmd("handleFocusChange",0);
        inx(id).cmd("handleFocusChange",1);
        inx.focusManager.checkSmoothFocus(last,id);
    }
    


    // Возвращает общего предка
    this.checkSmoothFocus = function(c1,c2) {
    
        var c1 = inx(c1);
        var c2 = inx(c2);
        // Составляем цепочки предков для c1 и c2.
        // Если null, то цепочка = {}
        var o1 = [];
        while(c1.exists()){ o1.unshift(c1.id());c1=c1.owner(); }
        var o2 = [];
        while(c2.exists()){ o2.unshift(c2.id());c2=c2.owner(); }

        for(var i=0;i<o1.length;i++)
            if(o1[i]!=o2[i])
                inx(o1[i]).cmd("handleSmoothBlur");
    }
    
    this.handleMousedown = function(e) {
        inx.focusManager.lastEvent = e;
        inx.focusManager.clickEnabled = true;
        var cmp = inx.cmp.fromElement(e.target);
        cmp.cmd("mousedown",e);
        inx.focusManager.focus(cmp.id());
    }
    
    this.handleMouseup = function(e) {
        var cmp = inx.cmp.fromElement(e.target);
        cmp.cmd("mouseup",e);
    }    
    
    this.handleClick = function(e) {
    
        if(!inx.focusManager.clickEnabled) {
            return;
        }
    
        var cmp = inx.cmp.fromElement(e.target);
        //cmp.cmd("click",e);
        cmp.cmd("click",e);
    }
    
    this.handleDblClick = function(e) {
        var id = inx.cmp.fromElement(e.target).cmd("dblclick",e);
    }
    
    this.handleMouseMove = function(e) {
        inx.focusManager.lastEvent = e;
    }
    
    this.checkActivity = function(e) {
        var e = inx.focusManager.lastEvent;
        if(!e) return;
        var hash = e.pageX+":"+e.pageY;
        if(hash!=inx.focusManager.lastHash) {
            inx.focusManager.hashTime = new Date().getTime();
            inx.focusManager.first = true;
            inx.help.hide();
        } else {
            if(inx.focusManager.first && new Date().getTime()-inx.focusManager.hashTime>1000) {
                inx.focusManager.first = false;                
                var id = inx.cmp.fromElement(e.target).id();
                inx.help.show(id,e.pageX,e.pageY);
            }
        }
        inx.focusManager.lastHash = hash;
    }
    
    this.cmp = function() { return inx(this.focused) }
}

$(document).mousemove(inx.focusManager.handleMouseMove);
$(document).mousedown(inx.focusManager.handleMousedown);
$(document).mouseup(inx.focusManager.handleMouseup);
$(document).click(inx.focusManager.handleClick);
$(document).dblclick(inx.focusManager.handleDblClick);
$(document).bind('contextmenu', function(e) { inx.focusManager.cmp().cmd("contextMenu",e); } );

setInterval(inx.focusManager.checkActivity,200);