if(!window.ndra)
	window.ndra = {};

ndra.menu = function(menu,submenu,p) {

    if(!p)
		p = {};
    
    if(!p.offset)
		p.offset = 0;
    
    /**
     * Возвращает элемент меню
     * Аргументом может быть как атрибут menu:id элемента, так и его порядковый номер
     **/
    var getItem = function(id) {
    
        var ret = null;
		$(menu).each(function( ) {
			if($(this).attr("menu:id")==id) {
				ret = $(this);
			}
		});
		return $(ret);
    }
    
    // Возвращает элемент субменю
    var getSubitem = function(id) {
        var ret = null;
        $(submenu).each(function(){
			if($(this).attr("menu:id")==id)
				ret = $(this);
		});
		return $(ret);
    }
    
    var show = function (n) {
    
		// Коллекция элементов меню
        var items = $(menu);
        // Активный элемент меню
        var item = getItem(n);
        
        if(!item.length)
			return;
			
        // Коллекция элементов субменю
        var subitems = $(submenu);
        // Активный элемент субменю
        var subitem = getSubitem(n);
        
        if(!subitem.length)
            return;
            
        subitems.css({
			display:"none"
		});
		
        items.removeClass("ndra-menu-active");
        item.addClass("ndra-menu-active");

		// Определяем поправку смещения субменю
		var ruler = $("<div>").css({
		    left:0,
		    top:0,
		    position:"absolute",
			display:"block"
		});
		ruler.appendTo(subitem.parent());
		var submenuOffset = ruler.offset().left;
		ruler.remove();
		
		var left = item.offset().left;
		
		// Учитываем смещение из запроса
		left+= p.offset;
		
		// Насколько субменю вылезло за правй край экрана
		var right = left + subitem.outerWidth() + 10 - $("body").width();
		if(right<0)
		    right = 0;
		
		left-= right;
		left-= submenuOffset;
		
        subitem.css({
            position:"absolute",
            display:"block",
			left:left
		});
    }
    
    var hide = function() {
        $(menu).removeClass("ndra-menu-active");
        $(submenu).hide();
    }

    $(menu).mouseenter(function() {
        hide();
        var id = $(this).attr("menu:id");
		show(id);
    })

    var closeAll = function() {
		hide();
	}

    var timer;
    var leave = function() {
        timer = setTimeout(closeAll,500);
    }

    var enter = function() {
        try {clearTimeout(timer);} catch(ex) {}
    }

    $(menu).mouseleave(leave);
    $(submenu).mouseleave(leave);
    $(menu).mouseenter(enter);
    $(submenu).mouseenter(enter);

}
