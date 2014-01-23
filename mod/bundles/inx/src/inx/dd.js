inx.dd = {

    current:{},

    /**
     * Делает элемент перетаскиваемым
     **/
    enable:function(element,obj,fn,params) {
    
        element = $(element);    
        params = params || {};
        
        params.element = element;
        params.obj = (obj).id();
        params.fn = fn;
        
        // Смещение, до которого перетаскивание не начинается
        // По умолчанию - 10px
        if(params.offset===undefined) {
            params.offset = 10;
        }
    
        element.mousedown(function(event){
            window.focus();
            event.preventDefault();
            inx.dd.handleMouseDown(event,params)
        });        
        
    }, 
    
    /**
     * Обновляет положение и размеры прямоугольника перетаскивания
     **/
    updateHelper:function() {
    
        if(!inx.dd.helper) {
            return;
        }
        
        if(!inx.dd.current.element) {
            return;
        }
        
        var event = inx.dd.current.event;
    
        var width = inx.dd.current.element.width();
        var height = inx.dd.current.element.height();
    
        // Лево
        inx.dd.helper[0].css({
            left:event.pageX - inx.dd.current.elementOffset.x,
            top:event.pageY - inx.dd.current.elementOffset.y,
            width:1,
            height:height
        });
        
        // Право
        inx.dd.helper[1].css({
            left:event.pageX + width - inx.dd.current.elementOffset.x,
            top:event.pageY - inx.dd.current.elementOffset.y,
            width:1,
            height:height
        });
        
        // Верх
        inx.dd.helper[2].css({
            left:event.pageX - inx.dd.current.elementOffset.x,
            top:event.pageY - inx.dd.current.elementOffset.y,
            width:width,
            height:1
        });
        
        // Низ
        inx.dd.helper[3].css({
            left:event.pageX - inx.dd.current.elementOffset.x,
            top:event.pageY + height - inx.dd.current.elementOffset.y,
            width:width,
            height:1
        });
    
    },
    
    /**
     * Показывает хэлпер
     **/
    showHelper:function() {
    
        // Создаем хэлпер - прямоугольник, который мы перетаскиваем
        if(!inx.dd.helper) {
        
            inx.dd.helper = [];
        
            for(var i=0;i<4;i++) {
                inx.dd.helper[i] = $("<div>").css({
                    display:"none",
                    position:"absolute",
                    zIndex:1000000,
                    background:"blue"
                }).appendTo("body");
            }
        }
    
        for(var i=0;i<4;i++) {
            inx.dd.helper[i].css({display:"block"})
        }
        
        inx.dd.updateHelper();
    },
    
    /**
     * Скрывает хэлпер
     **/
    hideHelper:function() {
    
        if(inx.dd.helper) {
            for(var i=0;i<4;i++) {
                inx.dd.helper[i].css({display:"none"})
            }
        }
    },
    
    handleMouseDown:function(event,params) {
    
        // Запоминаем параметры активного перетаскивания
        inx.dd.current = inx.deepCopy(params);    
        
        // Передаем управление обработчику событий мыши    
        inx.dd.handleMouseEvent(event);   
        
        //inx.dd.enabledFlag = true;

    },
    
    handleMouseEvent:function(event) { 
    
        inx.dd.current.event = event;   
    
        //  Быстрая ссылка на перетаскиваемый элемент
        var element = inx.dd.current.element;    
            
        // Если событие - нажатие на кнопку мыши, запоминаем проводим инициализацию:
        // Запоминаем начальные координаты и сбрасываем флаги
        switch(event.type) {
        
            case "mousedown":            
        
                // Начальные координаты курсора
                inx.dd.current.startX = event.pageX;
                inx.dd.current.startY = event.pageY;
                
                // Координаты курсора в прошлом вызове этой функции (равны текущим, т.к. вызов первый)
                inx.dd.current.lx = event.pageX;
                inx.dd.current.ly = event.pageY;
                
                // Смещение курсора относительно перетаскиваемого элемента
                // На момент начала перетаскивания
                inx.dd.current.elementOffset = {
                    x:event.pageX - element.offset().left,
                    y:event.pageY - element.offset().top
                }
                
                inx.dd.current.phase = "start";
                inx.dd.current.active = true;
                
                break;
                
            case "mousemove":

                inx.dd.current.phase = "move";                
                inx.dd.updateHelper();                
                break;
                
            case "mouseup":
            
                inx.dd.current.phase = "stop";                
                inx.dd.hideHelper();      
                
                setTimeout(function() {
                    inx.dd.enabledFlag = false;
                });
                          
                break;
            
        }        
        
        // Общие для всех событий параметры
        
        // Смещение с прошлого раза 
        inx.dd.current.dx = event.pageX - inx.dd.current.lx;
        inx.dd.current.dy = event.pageY - inx.dd.current.ly;
        
        // Запоминаем координаты прошлого раза
        inx.dd.current.lx = event.pageX;
        inx.dd.current.ly = event.pageY;
        
        // Смещение с начала перетаскивания
        inx.dd.current.ax = event.pageX - inx.dd.current.startX;
        inx.dd.current.ay = event.pageY - inx.dd.current.startY;
        
        // Вычисляем смещения от начала перетаскивания
        inx.dd.current.distance = Math.sqrt(inx.dd.current.ax*inx.dd.current.ax + inx.dd.current.ay*inx.dd.current.ay);            
        
        // Если смещение превышает порог - начинаем перетаскивание
        if(inx.dd.current.distance >= inx.dd.current.offset && !inx.dd.current.thresold) {

            if(inx.dd.current.phase!="start") {
                var params = inx.deepCopy(inx.dd.current);
                params.phase = "start";
                inx.dd.fireEvent(params);   
                inx.dd.enabledFlag = true;
            }
            
            inx.dd.current.thresold = true;       
              
        }
        
        if(inx.dd.current.active && inx.dd.current.thresold) {
            inx.dd.fireEvent(inx.dd.current);
        }       
        
        // Убираем параметры текущего перетаскивания
        if(event.type=="mouseup") {
            inx.dd.current = {};
        }
        
    },
    
    enabled:function() {
        return !!inx.dd.enabledFlag;
    },
    
    fireEvent:function(params) {
    
        var ret = inx(params.obj).cmd(params.fn,params,params);
        
        if(params.phase==="start") {
            if(ret===false) {
                inx.dd.current = {}
            } else {  
            
                // Если начали перетаскивание - запрещаем событие клик
                inx.focusManager.clickEnabled = false;  
            
                if(params.helper) {
                    inx.dd.showHelper();        
                }
            }
        }
    }

}

$(document).mouseup(inx.dd.handleMouseEvent);
$(document).mousemove(inx.dd.handleMouseEvent);