// @include inx.dialog
// @link_with_parent

inx.mod.reflex.fields.point.picker = inx.dialog.extend({

    constructor:function(p) {
    
        p.style = {
            width: 800,
            height: 500
        }
        
        p.destroyOnEscape = true;
        
        this.textfield = inx({
            type: "inx.textfield"            
        })
        
        p.bbar = [this.textfield, {
            type: inx.button,
            text: "Сохранить",
            onclick: [this.id(), "setAndClose"]
        }];
        
        if(!p.value) {
            p.value = "37.618027343748714, 55.73521303739836";
        }
        
        p.title = "Укажите точку на карте";
        this.base(p);
        this.on("render","createMap")
    },
    
    cmd_setAndClose: function() {
        this.fire("setValue", this.textfield.info("value"));
        this.task("destroy");
    },
    
    cmd_createMap: function() {
    
        var id = inx.id();
        var $e = $("<div>").attr("id", id).css({
            width: this.info("innerWidth"),
            height: 441
        });
        this.cmd("html", $e);
        $e.html();
        
        var cmp = this;
        
        setTimeout(function() {
        
            //сюда кладем коорды после различных манипуляции с точкой, они в начале центр каарты и начальные коорды плейсмарка
            var coords = cmp.value.split(",");
            
            cmp.cmd("setCoords", coords);
            
            var myMap = new ymaps.Map(id, {
                center: coords,
                zoom: 13,
                controls: ['zoomControl','rulerControl']
            });
            
            //добавляем плейcмарк на карту, и потом с ней работаем
            positionPointer = new ymaps.Placemark(coords, {}, {draggable:true, hasBalloon:false});
            //при перетасикивании точки записываем в переменую текущие координаты точки
            positionPointer.events.add("dragend",function(e,x) {
                coords = positionPointer.geometry.getCoordinates();
                cmp.cmd("setCoords", coords);
            });
            myMap.geoObjects.add(positionPointer);
            
            //добавляем контрол поиска на карту, и запрещаем ему добавлять результаты поиска на карту
            var searchControl = new ymaps.control.SearchControl({
                options: {
                    noPlacemark: true
                }
            });
                    
            myMap.controls.add(searchControl);
            
            //обработчик события показов результатов поиска
            searchControl.events.add('resultshow', function () {
                //получаем индекс выбраного результата поиска( это клацный из списка элемент)
                var selected = searchControl.getSelectedIndex();
                //и по нему берем сам результат из массива
                var result = searchControl.getResultsArray()[selected];
                
                //получем его координаты и устанавливаем их нашей точке
                coords = result.geometry.getCoordinates();
                positionPointer.geometry.setCoordinates(coords);
                cmp.cmd("setCoords", coords);
        
            }, this);
            
            //обработчик дубль клика на карте
            myMap.events.add('dblclick', function (e) {
                //из события получаем коорды и ставим их нашей точке
                coords = e.get('coords');
                cmp.cmd("setCoords", coords);
                positionPointer.geometry.setCoordinates(coords);
            });
            
             /**
            * Ололо началось
            **/
            
            if(cmp.address) {
                // Осуществляет поиск объекта по адресу.
                // Полученный результат показываем на карте ввиде нашей метки.
                var myGeocoder = ymaps.geocode(cmp.address);
                myGeocoder.then(
                    function (result) {
                        coords = result.geoObjects.get(0).geometry.getCoordinates();
                        positionPointer.geometry.setCoordinates(coords);
                        myMap.panTo(coords);   
                        cmp.cmd("setCoords", coords);                 
                    },
                    function (err) {
                        // обработка ошибки
                        inx.msg(err);
                    }
                );
            }

            /**
            * Ололо закончилось
            **/
                   
        }, 10);
        
    },
    
    cmd_setCoords: function(coords) {
        this.textfield.cmd("setValue", coords);
    }
    
});