if(!window.ndra)
    window.ndra = {};

ndra.carousel = function(id) {
    var inst = $(id).data("carousel");
    return inst;
}

// Экземпляр карусели
ndra.carousel.instance = function(e,p) {

    var carousel = this;

    e = $(e);
    if(e.data("sqm1h3k96")) return;
    e.data("sqm1h3k96",true);

    var that = this;
    
    // Выставляем настройки
    if(!p)
        p = {};
        
    // Высота
    p.height = p.height || 200;
    
    // Минимальная ширина
    p.minWidth = p.minWidth || 200;
    
    //Вертикальная карусель
    p.vertical = p.vertical || false;
    
    // Расстояние между элементами
    if(p.spacing===undefined)
        p.spacing = 20;
        
    // Смещение - на сколько элементов стрелки перематывают карусель
    p.offset = p.offset || "auto";
    
    // Функция, предотвращающая действия по умочанию
    var prevent = function(e) {
        e.preventDefault();
    }
    
    p.prev = p.prev || ".ndraCarouselPrev";
    p.next = p.next || ".ndraCarouselNext";
    p.prevDisabled = p.prevDisabled || "ndraCarouselPrevDisabled";
    p.nextDisabled = p.nextDisabled || "ndraCarouselNextDisabled";
    
    p.container = p.container || ".ndraCarouselContainer";
    p.navigation = p.navigation || ".navigation";
    p.navigationActive = p.navigationActive || "active";

    // Находим элементы карусели по селекторам
    var container = e.find(p.container);
    var prevButton = e.find(p.prev);
    var nextButton = e.find(p.next);
    var navigation = e.find(p.navigation);
    
    var slider = $("<div>").css({
        position:"absolute",
        left:0,
        top:0
    });
    
    if (p.vertical) {
        container.css({
            overflow:"hidden",
            position:"relative",
            width:p.height
        });
    } else {
        container.css({
            overflow:"hidden",
            position:"relative",
            height:p.height
        });
    }
    
        

    container.children().css({
        position:"absolute",
        top:0,
        margin:0,
        border:"none"
    }).appendTo(slider);
    slider.appendTo(container);
    
    var total = slider.children().length;

    var current = 0;

    prevButton.css({
        cursor:"pointer",
        "-webkit-user-select":"none"
    }).click(function(){
        that.prev()
    }).dblclick(prevent);
    
    nextButton.css({
        cursor:"pointer",
        "-webkit-user-select":"none"
    }).click(function(){
        that.next()
    }).dblclick(prevent);
    
    navigation.children().each(function(n){
        $(this).click(function(){
            carousel.moveTo(n);
        })
    });

    // Возвращает число элементов, на которое нужно сдвинуть строку
    this.offset = function() {
        var ret = p.offset=="auto" ? this.visible() : p.offset;
        if(ret<1) ret = 1;
        return ret;
    }

    /**
     * перематывает карусель на один шаг вперед
     **/
    this.next = function(cycle) {
        current+=that.offset();
        if((cycle||p.cycle) && current>total-that.visible())
            current = 0;
        that.moveTo(current);
    }

    /**
     * перематывает карусель на один шаг назад
     **/
    this.prev = function(cycle) {
        current-=that.offset();
        if((cycle||p.cycle) && current<0)
            current = total - that.visible();
        that.moveTo(current);
    }

    var lastChangeTime = new Date().getTime();

    this.moveTo = function(n,immediately) {
    
        current = n*that.offset();
        var visible = that.visible();
        if(!p.cycle) {
            if(current+visible>total) current = total-visible;
            if(current<0) current = 0;
        }else{
            if(current+visible>total) current = 0;
            if(current<0) current = total-visible;    
        }

        // Обновляем кнопки туда-сюда
        // Еслик включено зацикливание, кнопки не обновляются
        if(!p.cycle) {
        
            if(current==0) {
                prevButton.addClass(p.prevDisabled);
            } else {
                prevButton.removeClass(p.prevDisabled);
             }

            if(current==total-visible) {
                nextButton.addClass(p.nextDisabled);
            } else {
                nextButton.removeClass(p.nextDisabled);
            }
        }
        
        // Останавливаем текущую аниацию и прыгаем в конец
        slider.stop(true,true); 
        
        
        //Вертикальная карусель
        if (p.vertical) {
        
            var offset = - slider.children().eq(current).offset().top + slider.offset().top;
            
            // Если велючена immediately==false, перемытываем карусель плавно
            // Если выключена - просто перепрыгиваем
            if(!immediately)
                slider.animate({top:offset});
            else
                slider.css({top:offset});
                
        } else {
            var offset = - slider.children().eq(current).offset().left + slider.offset().left;
            
            // Если велючена immediately==false, перемытываем карусель плавно
            // Если выключена - просто перепрыгиваем
            if(!immediately)
                slider.animate({left:offset});
            else
                slider.css({left:offset});
        }
        
        

        // Подсвечиваем навигацию
        navigation.children().removeClass(p.navigationActive);
        navigation.children().eq(current/that.offset()).addClass(p.navigationActive);

        if(!immediately)
            lastChangeTime = new Date().getTime();
    }

    /**
     * Возвращает число видимых элементов
     **/
    this.visible = function() {
        
        if (p.vertical) {
            var lengthOfBlock = container.height();
        } else {
            var lengthOfBlock = container.width();
        }
        
        var n = Math.floor((lengthOfBlock + p.spacing) / (p.minWidth + p.spacing));
        
        if(n < 1) n = 1;
        return n;
    }

    var lastWidth = -1;
    
    
    mod.on("carousel-update", function() {
        console.log(1);
        p.delay = 10;
        that.update();
    });
    
    /**
     * Обновляет количество элементов
     * Вызывается переодически и при ресайзе страницы
     **/
    this.update = function(what) {

        if (p.delay) {
            time = new Date().getTime() - lastChangeTime;
            if (time > p.delay * 1000) that.next(true);
        }
        
        if (p.vertical) {
            var containerWidth = container.height();
        } else {
            var containerWidth = container.width();
        }
        
        
        
        if(containerWidth==lastWidth) return;
        lastWidth = containerWidth;
        var items = slider.children();
        var n = that.visible();
        var itemWidth = (containerWidth+p.spacing)/n-p.spacing;
        items.each(function(i){
            var x1 = Math.floor((itemWidth+p.spacing)*i);
            var x2 = Math.floor((itemWidth+p.spacing)*(i+1));
            var width = x2-x1-p.spacing;
            
            if (p.vertical) {
            
                width -= $(this).outerHeight() - $(this).height();
                $(this).css({
                    height:width,
                    top:x1
                });
                
            } else {
            
                width -= $(this).outerWidth() - $(this).width();
                $(this).css({
                    width:width,
                    left:x1
                });
                
            }
            
        });
        that.moveTo(current,true);
    }

    $(window).resize(this.update);
    setInterval(this.update,1000);
    setTimeout(this.update,0);

}

// Создает карусель
ndra.carousel.create = function(what,p) {

    $(what).each(function() {
        var inst = new ndra.carousel.instance($(this),p);
    });

}
