/*-- /mod/bundles/inx/src/inx.js --*/


/**
 * Основная функция inx()
 * По смыслу похожа на $() в jquery и выполняет множество функций
 **/
inx = function(p,type) {

    if(!p) {
        p=0;
    }
    
    switch(typeof(p)) {
    
        case "number":
        case "string":
            return new inx.cmp(p);
            break;
            
        case "object":
        
            if(p instanceof Array) {
                return new inx.cmp(p);
            }

             // Роутер
            if(p.xyaz3m9ijf1hnw5zt890) {
                return p;
            }

            // Объект
            if(p.bj852tc92op9zqyli3f5) {
                return inx(p.id());
            }

            if(!p.type) {
                p.type = type;
            }

            if(p.type) {
                return inx.cmp.create(inx.deepCopy(p));
            }
            
            break;
    }
  
    inx.msg("inx(..) unsupported argument type: "+typeof(p),1);
    return inx(0);
}

inx.handlers = {}

inx.conf = {
    url:"/inx/pub/",
    res:"/inx/res/",
    cmdUrl:"/mod_json/",
    z_index_dialog:100000000,
    z_index_message:120000000,
    z_index_field:110000000,
    ajaxIndicator:true,
    componentLoadingHTML:"Загрузка..."
};

inx.on = function() {
    inx.service("events").on.apply(null,arguments);
}

/**
 * Вызывает глобальное событие name
 **/
inx.fire = function() {
    return inx.service("events").fire.apply(null,arguments);
}

/**
 * Создает и возвращает функцию для вызова метода fn в контексте scope м параметрами p
 * Функция при этом не вызывается, вы можете вам нужно вызвать ее или установить как
 * обработчик на какое-нибудь событие
 **/
inx.delegate = function(fn,scope,p) {
    if(p) {
        return  function() {
            return fn.apply(scope,[p]);
        }
    } else {
        return  function() {
            return fn.apply(scope,arguments);
        }
    }
}

inx.cmd = function(id,cmd,p1,p2,p3) {
    if(p1!==undefined) {
        return function() {
            inx(id).cmd(cmd,p1,p2,p3);
        }
    } else {
        return function(p) {
            inx(id).cmd(cmd,p);
        }
    }
}

/**
 * Создает неймспейс - пустой объект
 * К примеру, inx.ns("inx.mod.reflex") создаст объект inx.mod.reflex
 * Если объект с требуемым имененм существует, он не будет изменен
 **/
inx.ns = function(ns) {
    ns = ns.split(".");
    var obj = inx;
    for(var i=1;i<ns.length;i++) {
        if(!obj[ns[i]]) {
            obj[ns[i]] = {};
        }
        obj = obj[ns[i]];
    }
    return obj;
}

/**
 * Добавляет стиль в документ
 * Берет строку с css и добавляет ее в тэг <style> в <head>
 * Аргументы - набор строк css (Можно одну, можно несколько - несколько аргументов)
 **/
inx.css = function() {

    var a = [];
    for(var i=0;i<arguments.length;i++) {
        a.push(inx.path(arguments[i]));
    }
        
    a = a.join("\n");    
    $("<style>"+a+"</style>").appendTo("head");
}

/**
 * Используется для преобразования url
 * %res% => Адрес директории в которой хранятся изображения
 **/
inx.path = function(path) {
    path = path.replace("%res%",inx.conf.res);
    return path;
}

inx.geq = function(a,b) {
    a = parseInt(a);
    if(!a) a = 0;
    if(a<b)a=b;
    return a;
}

inx.strPadLeft = function(str,count) {
    str+="";
    var ret = "";
    for(var i=0;i<count;i++)
        ret += (i>=count-str.length) ? str.substr(str.length-count+i,1) : "0";
    return ret;
}

inx.strPadRight = function(str,count) {
    str+="";
    var n = count-str.length;
    if(n<0) n=0;
    str+="0000000000".substr(0,n);
    return str;
}

/**
 * Сериализует объект в строку
 * На данный момент невозможно обратно развернуть строку в объект и метод используется только для созданя хэша объекта
 **/
inx.serialize = function(a) {
    
    if(typeof(a)=="object") {
        var ret = ""
        for(var i in a)
            ret += inx.serialize(a[i])+":";
        return ret;
    }
    return a;
}

/**
 * Вычисляет crc32-хэш из строки
 **/
inx.crc32 = function(str) {

    str = inx.serialize(str);

    var table = "00000000 77073096 EE0E612C 990951BA 076DC419 706AF48F E963A535 9E6495A3 0EDB8832 79DCB8A4 E0D5E91E 97D2D988 09B64C2B 7EB17CBD E7B82D07 90BF1D91 1DB71064 6AB020F2 F3B97148 84BE41DE 1ADAD47D 6DDDE4EB F4D4B551 83D385C7 136C9856 646BA8C0 FD62F97A 8A65C9EC 14015C4F 63066CD9 FA0F3D63 8D080DF5 3B6E20C8 4C69105E D56041E4 A2677172 3C03E4D1 4B04D447 D20D85FD A50AB56B 35B5A8FA 42B2986C DBBBC9D6 ACBCF940 32D86CE3 45DF5C75 DCD60DCF ABD13D59 26D930AC 51DE003A C8D75180 BFD06116 21B4F4B5 56B3C423 CFBA9599 B8BDA50F 2802B89E 5F058808 C60CD9B2 B10BE924 2F6F7C87 58684C11 C1611DAB B6662D3D 76DC4190 01DB7106 98D220BC EFD5102A 71B18589 06B6B51F 9FBFE4A5 E8B8D433 7807C9A2 0F00F934 9609A88E E10E9818 7F6A0DBB 086D3D2D 91646C97 E6635C01 6B6B51F4 1C6C6162 856530D8 F262004E 6C0695ED 1B01A57B 8208F4C1 F50FC457 65B0D9C6 12B7E950 8BBEB8EA FCB9887C 62DD1DDF 15DA2D49 8CD37CF3 FBD44C65 4DB26158 3AB551CE A3BC0074 D4BB30E2 4ADFA541 3DD895D7 A4D1C46D D3D6F4FB 4369E96A 346ED9FC AD678846 DA60B8D0 44042D73 33031DE5 AA0A4C5F DD0D7CC9 5005713C 270241AA BE0B1010 C90C2086 5768B525 206F85B3 B966D409 CE61E49F 5EDEF90E 29D9C998 B0D09822 C7D7A8B4 59B33D17 2EB40D81 B7BD5C3B C0BA6CAD EDB88320 9ABFB3B6 03B6E20C 74B1D29A EAD54739 9DD277AF 04DB2615 73DC1683 E3630B12 94643B84 0D6D6A3E 7A6A5AA8 E40ECF0B 9309FF9D 0A00AE27 7D079EB1 F00F9344 8708A3D2 1E01F268 6906C2FE F762575D 806567CB 196C3671 6E6B06E7 FED41B76 89D32BE0 10DA7A5A 67DD4ACC F9B9DF6F 8EBEEFF9 17B7BE43 60B08ED5 D6D6A3E8 A1D1937E 38D8C2C4 4FDFF252 D1BB67F1 A6BC5767 3FB506DD 48B2364B D80D2BDA AF0A1B4C 36034AF6 41047A60 DF60EFC3 A867DF55 316E8EEF 4669BE79 CB61B38C BC66831A 256FD2A0 5268E236 CC0C7795 BB0B4703 220216B9 5505262F C5BA3BBE B2BD0B28 2BB45A92 5CB36A04 C2D7FFA7 B5D0CF31 2CD99E8B 5BDEAE1D 9B64C2B0 EC63F226 756AA39C 026D930A 9C0906A9 EB0E363F 72076785 05005713 95BF4A82 E2B87A14 7BB12BAE 0CB61B38 92D28E9B E5D5BE0D 7CDCEFB7 0BDBDF21 86D3D2D4 F1D4E242 68DDB3F8 1FDA836E 81BE16CD F6B9265B 6FB077E1 18B74777 88085AE6 FF0F6A70 66063BCA 11010B5C 8F659EFF F862AE69 616BFFD3 166CCF45 A00AE278 D70DD2EE 4E048354 3903B3C2 A7672661 D06016F7 4969474D 3E6E77DB AED16A4A D9D65ADC 40DF0B66 37D83BF0 A9BCAE53 DEBB9EC5 47B2CF7F 30B5FFE9 BDBDF21C CABAC28A 53B39330 24B4A3A6 BAD03605 CDD70693 54DE5729 23D967BF B3667A2E C4614AB8 5D681B02 2A6F2B94 B40BBE37 C30C8EA1 5A05DF1B 2D02EF8D";     
 
    var crc = 0; 
    var n = 0; //a number between 0 and 255 
    var x = 0; //an hex number 

    crc = crc ^ (-1); 
    for( var i = 0, iTop = str.length; i < iTop; i++ ) { 
        n = ( crc ^ str.charCodeAt( i ) ) & 0xFF; 
        x = "0x" + table.substr( n * 9, 8 ); 
        crc = ( crc >>> 8 ) ^ x; 
    } 
    return crc ^ (-1); 
}

/**
 * Вычисляет высоту элемента
 * Если элемент скрыт, клонирует его и меряет высоту
 **/ 
inx.height = function(e) {

    var t1 = new Date().getTime();
    var hname = "inx.height";

    var h = e.height();
    
    if(!h) {
        var ruler = inx.getRuler();
        var e2 = e.clone().appendTo(ruler);
        h = e2.height();
        hname = "inx.height(hidden)";
        e2.remove();
    }
    
    var t2 = new Date().getTime();
    var time = t2-t1;
    inx.observable.debug.cmdCountByName[hname] = (inx.observable.debug.cmdCountByName[hname] || 0) + 1;
    inx.observable.debug.totalTime[hname] = (inx.observable.debug.totalTime[hname] || 0) + time;
    
    return h;
}

inx.width = function(e,type) {

    switch(type) {
        default:
            fn = function(e) {
                return e.width();
            }
            break;
        case "client":
            fn = function(e) {
                return e.get(0).clientWidth;
            }
            break;
    }

    var w = fn(e);
    
    if(!w) {
        var ruler = inx.getRuler();
    
        var e2 = e.clone().appendTo(ruler);
        w = fn(e2);
  
    }
        
    return w;
}

setInterval(function() {
     inx.getRuler().html("");
},5000);

inx.getRuler = function() {
    if(!inx.ruler) {
        inx.ruler = $("<div>").addClass("inx-box").appendTo("body").css({
            position:"absolute",
            left:-10,
            top:-10,
            width:1,
            height:1,
            overflow:"hidden"
        });
    }
    return inx.ruler;
}

inx.deepCopy = function(obj) {

    if(obj instanceof Array)
        var clone = [];
    else
        var clone = {};
    
    for(var i in obj) {
        if(typeof(obj[i])=="object") {        
        
            // Простые объекты (массивы) мы клонируем
            if(obj[i] && (obj[i].constructor==({}).constructor || obj[i].constructor==([]).constructor )) {        
                clone[i] = inx.deepCopy(obj[i]);     
            }
            
            //  Объекты с прототипами (Объекты jquery, inx и т.п. - оставляем как есть)       
            else {
                clone[i] = obj[i];
            }            
            
        } else {
            clone[i] = obj[i];
        }
    }
    return clone;

}

inx.arrayMove = function (a, old_index, new_index) {
    while (old_index < 0) {
        old_index += a.length;
    }
    while (new_index < 0) {
        new_index += a.length;
    }
    if (new_index >= a.length) {
        var k = new_index - a.length;
        while ((k--) + 1) {
            a.push(undefined);
        }
    }
    a.splice(new_index, 0, a.splice(old_index, 1)[0]);
};

inx.__nextId = 0;
inx.id = function() {
    inx.__nextId++;
    return "inx-"+inx.__nextId;
}


/*-- /mod/bundles/inx/src/inx/service.js --*/


inx.service = function(name) {
    return inx.service.services[name];
}

inx.service.services = {}

inx.service.register = function(name,obj) {
    inx.service.services[name] = obj;    
}

/*-- /mod/bundles/inx/src/inx/key.js --*/


inx.key = new function() {

    var that = this;

    this.handlers = {};
    this.pressed = {};
    
    this.handleKeydown = function(e) {
    
        inx.key.pressed[e.keyCode] = true;
    
        // Определяем нажата ли клавиша комманд
        if((inx.key.pressed[91] || inx.key.pressed[93]) && e.metaKey) {
            e.commandKey = true;
        }

        // Клавиша Command воспринимается системой как Control
        if(e.commandKey) {
            e.ctrlKey = true;
        }
       
        that.fire(e,"keydown");

        var ret = [];

        if(e.shiftKey) {
            ret.push("shift")
        }

        if(e.ctrlKey) {
            ret.push("ctrl");
        }

        ret.push(e.which);

        var name = ret.join("+");
        name = that.normalizeEventName(name);

        that.fire(e,name);
    
    }

    this.normalizeEventName = function(name) {

        var keyAliases = {
            esc:27,
            enter:13,
            f1:112,
            f2:113,
            f3:114,
            f4:115,
            f5:116,
            tab:9
        }

        var s =(name+"").split("+");
        p = {};
        for (var i = 0; i < s.length; i++) {
            var part = s[i];
            if(part==parseInt(part)) p.keyCode=part;
            else if(part=="ctrl") p.ctrlKey = true;
            else if(part=="shift") p.shiftKey = true;
            else p.keyCode = keyAliases[part] || part.toUpperCase().charCodeAt();
        }

        var ret = [];

        if(p.shiftKey) {
            ret.push("shift")
        }

        if(p.ctrlKey) {
            ret.push("ctrl");
        }

        ret.push(p.keyCode);

        return ret.join("+");
    }
    
    that.handleKeypress = function(e) {
        if(!e.ctrlKey) {
            e.char = String.fromCharCode(e.which);
            that.fire(e,"keypress");
        }
    }
    
    that.handleKeyUp = function(e) {
        inx.key.pressed[e.keyCode] = false;
    }
    
    this.init = function() {        
        $(document).keypress(that.handleKeypress);
        $(document).keydown(that.handleKeydown);
        $(document).keyup(that.handleKeyUp);
        inx.service.register("key",that);
    }
    
    /**
     * Устанавливает обработчик на нажатие клавиши
     **/
    this.on = function(event,handler) {

        switch(event) {
            case "keydown":
            case "keypress":
                break;
            default:
                event = inx.key.normalizeEventName(event);
                break;
        }

        inx.on("key/"+event,handler);

    }
    
    this.fire = function(event,type) {
        var ret = inx.fire("key/"+type,event);
        if(ret===false) {
            event.preventDefault();
        }
    }
    
    $(function() {
        that.init();
    })

}


/*-- /mod/bundles/inx/src/inx/layout.js --*/


inx.layout = {}


/*-- /mod/bundles/inx/src/inx/layout/absolute.js --*/


inx.layout.absolute = {

    create:function() {
    },
    
    add:function(cmp) {
    
        if(cmp.data("ij89238v67"))
            return;
    
        cmp = inx(cmp);
        var e = $("<div>").css({
            position:"absolute"
        }).appendTo(this.__body);
        cmp.cmd("render");
        cmp.cmd("appendTo",e)
        cmp.data("layoutContainer",e);
        
        cmp.data("ij89238v67",true);
    },
    
    remove:function(cmp) {
        $(cmp.data("layoutContainer")).detach();
        cmp.data("ij89238v67",false);
    },
    
    sync:function() {    
    
        var width = this.info("clientWidth");
    
        var y = 0;
        this.items().each(function() {
        
            var e = this.data("layoutContainer");
            var top = this.info("param","y") || 0;
            y = Math.max(y,top+this.info("height"));
            
            e.css({
                left:this.info("param","x"),
                top:top
            });
            
            this.cmd("width",width);
            
        });
        
        
        
        this.cmd("setContentHeight",y);
    }
}


/*-- /mod/bundles/inx/src/inx/layout/column.js --*/


inx.layout.column = {

    add:function(id) {    
        
        var cmp = inx(id);   
        if(cmp.data("d4ubdy9sopvh7")) {
            return;
        }
        
        // Создаем элемент для компонента
            
        cmp = inx(cmp);
        var c = $("<div>").css({
            position:"absolute"
        }).appendTo(this.__body);
        
        cmp.cmd("render");
        cmp.cmd("appendTo",c)
        cmp.data("layoutContainer",c);
        
        cmp.data("d4ubdy9sopvh7",true);
    },    
    
    create:function() {        
    },
    
    sync:function() {
    
        var x = 0;
        var baseline = 0;
        var line = [];
        var xspacing = this.style("spacing");
        var yspacing = xspacing;
        var that = this;
        
        this.items().each(function() {
            this.data("xij89238v67-height",this.info("height"));
        }); 
        
        var completeLine = function() {
        
            if(!line.length) {
                return;
            }
        
            // Определяем высоту линии элементов
            var height = 0;
            for(var i in line) {
                height = Math.max(height,line[i].data("xij89238v67-height"));
            }

            // Центруем элементы по вертикали
            for(var i in line) {
                var e = line[i].data("layoutContainer");
                var top = baseline;
                
                if(that.style("valign")=="center") {
                    top-= line[i].data("xij89238v67-height")/2;
                    top+= height/2;
                }
                
                e.css({
                    top:top
                })
            } 
            
            baseline+= height+yspacing;
            line = [];  
            x = 0;
            
        }
        
        var clientWidth = this.info("clientWidth");
        
        if(clientWidth<1) {
            return;
        }
        
        this.items().cmd("width",clientWidth);
        
        this.items().each(function() {    
        
            if(!this.info("layoutReady")) {
                return;
            }
        
            if(this.style("break")) {
                completeLine();
            }
        
            e = this.data("layoutContainer");
            
            if(this.info("visible")) {
            
                var width = this.info("width");
                
                if(x + width > clientWidth) {
                    completeLine();
                }
                
                e.css({
                    left:x,
                    display:"block"
                });
                
                line.push(this);
                
                x += this.info("width");
                x+= xspacing;
            
            } else {
                e.css({
                    display:"none"
                })
            }
                
        });
        
        completeLine();
        this.cmd("setContentHeight",baseline - yspacing);
        
    },  
     
    remove:function(cmp) {
        $(cmp.data("layoutContainer")).detach();
        cmp.data("d4ubdy9sopvh7",false);
    }
}


/*-- /mod/bundles/inx/src/inx/layout/default.js --*/


inx.css(".f50tpvh3plh-label{ padding-left:30px;cursor:pointer;background-repeat:no-repeat;}")

inx.layout["default"] = {

    create:function() {},
    
    add:function(id) {
    
        var cmp = inx(id);    
        if(cmp.data("ij89238v67")) {
            return;
        }
            
        // Фон панели
       /* var bg = $("<div>").css({
            position:"absolute",
            background:"red"
        }).appendTo(this.__body);
        cmp.data("layoutBackground",bg); */
            
        // Контейнер панели
        var e = $("<div>").css({
            position:"absolute"
        }).appendTo(this.__body);
        cmp.data("layoutContainer",e);
                
        // Контейнер заголовка
        var te = $("<div>").addClass("f50tpvh3plh-label").css({
            position:"absolute",
            visibility:"hidden"
        }).click(inx.cmd(cmp,"toggle"))
        .appendTo(this.__body);
        cmp.data("titleContainer",te);        
        
        cmp.cmd("appendTo",e);
        
        cmp.data("ij89238v67",true);        
    },
    
    remove:function(cmp) {
        $(cmp.data("layoutContainer")).detach();
        $(cmp.data("titleContainer")).detach();
        cmp.data("ij89238v67",false);
    },
    
    sync:function() {   
    
        var padding = this.style("padding");
        var width = this.info("clientWidth");
       
        if(width<=0) {
            return;
        }
                    
        var that = this;
        
        if(this.private_html===undefined) {
            
            var y = 0;
            var spacing = this.style("spacing");                
            
            // Выставляем всем 
            this.items().cmd("width",width); 
            
            // Вычисляем заранее высоту каждого компонента
            this.items().each(function() {
                this.data("xij89238v67-height",this.info("height"));
            }); 
    
            this.items().each(function(n) {
                            
                if(!this.info("layoutReady")) {
                    return;
                }
            
                var areaHeight = 0;
                var areaStart = y;
            
                var e = this.data("layoutContainer");
                if(!e) {
                    return;
                }
                
                var doSpacing = false;
                
                var title = this.info("title");
                var t = this.data("titleContainer");   
                
                if(title) {
                                          
                    t.html(this.info("title"));
                    
                    t.css({
                        visibility:"visible"
                    });
                    
                    t.css({
                        top:y,
                        left:0,
                        width:width-30, // Поправка на padding
                        display:"block",
                        backgroundImage:"url("+inx.img(this.info("hidden") ? "expand" : "collapse")+")"
                    })
                    
                    var h = inx.height(t);
                    y += h;
                    areaHeight += h;
                    
                    if(this.info("visible")) {
                        y += that.style("titleMargin");                    
                    }
                    
                    doSpacing = true;
                    
                } else {
                    t.css("display","none");
                }
                
                if(this.info("visible")) {
                
                    e.css({
                        left:0,
                        top:y,
                        display:"block"
                    });
                   
                    var h = this.data("xij89238v67-height");
                    
                    areaHeight +=h ;
                    
                    y += h; 
                    
                    doSpacing = true;
                    
                } else {
                    e.css("display","none");
                }
                
                if(doSpacing) {
                    y += spacing;
                }
    
            })            
            
            y-= spacing;            
            this.cmd("setContentHeight",y);
            
        }     

    }
}


/*-- /mod/bundles/inx/src/inx/layout/fit.js --*/


inx.layout.fit = {

    create:function() {},
    
    add:function(cmp) {
    
        if(cmp.data("ij89238v67"))
            return;
    
        cmp = inx(cmp);
        
        var e = $("<div>").css({
            position:"absolute"
        }).appendTo(this.__body);
        
        if(!this.keepBorder)
            cmp.cmd("border",0);
            
        cmp.cmd("render");
        cmp.cmd("appendTo",e);
        cmp.data("layoutContainer",e);
        
        if(this.style("height")=="parent")
            cmp.style("height","parent");
        
    },
    
    remove:function(cmp) {
        $(cmp.data("layoutContainer")).detach();
        cmp.data("ij89238v67",false);
    },
    
    sync:function() {  
    
        var autoHeight = this.style("height")=="content";
        var width = this.info("clientWidth");
        var height = this.info("clientHeight");
        var contentHeight = 0;
        
        if(width<=0)
            return;
        
        this.items().each(function() {
        
            var e = this.data("layoutContainer");
            if(!e)
                return;
        
            if(this.info("visible")) {
                this.cmd("width",width);
                
                if(!autoHeight)
                    this.cmd("height",height);   
                    
                e.css({
                    left:0,
                    top:0,
                    display:"block"
                })
                
                contentHeight = this.info("height");
                    
            } else {
                e.css("display","none");
            }
        });
        
        this.cmd("setContentHeight",contentHeight);     
        
    }
}


/*-- /mod/bundles/inx/src/inx/layout/form.js --*/


inx.css(".f1bqm1 {position:absolute; color:black; opacity:.7; font-style:italic }");

inx.layout.form = {

    create:function() {},
    
    add:function(cmp) {  
    
        if(cmp.data("ij89238v67"))
            return;       
      
        cmp = inx(cmp);        
        var label = $("<div>").appendTo(this.__body).addClass("f1bqm1").text(cmp.info("param","label") || "");
        var c = $("<div>").css({position:"absolute"}).appendTo(this.__body);
        cmp.cmd("render");
        cmp.cmd("appendTo",c);
        cmp.data("label",label);
        cmp.data("container",c); 
        
        cmp.data("ij89238v67",true);      
    },

    remove:function(cmp) {
        $(cmp.data("container")).detach();
        $(cmp.data("label")).detach();
        cmp.data("ij89238v67",false);
    },
    
    sync:function() {   
    
        var bodyWidth = this.info("clientWidth");
        var p = 0;
        var y = 0;
        var that = this;
        var spacing = this.style("spacing");
        
        this.items().each(function(n) {
        
            var item = this;
            var container = item.data("container");
            var label = item.data("label");
        
            if(this.info("visible")) {
            
                container.css("display","block");
                label.css("display","block");
        
                label.html(item.info("param","label") || "");
                
                var help = item.info("help");
                if(help)
                    $("<span>").html("?").css({
                        marginLeft:10,
                        borderBottom:"1px solid gray",
                        cursor:"pointer"
                    }).attr("title",help)
                    .appendTo(label);
                
                // Расчитываем ширину лэйбла
                var lw = item.info("param","labelWidth");
                if(lw===undefined)
                    lw = that.labelWidth;
                if(lw===undefined)
                    lw = 150;
                switch(item.info("param","labelAlign")) {
                
                    case "left":
                    
                        var tmplw = Math.max(lw-15,0);
                        label.css({
                            left:0,
                            top:y,
                            width:tmplw
                        });
                        container.css({
                            left:lw,
                            top:y,
                            width:bodyWidth-lw
                        });
                        y+= Math.max(label.height(),item.info("height"));
                        this.cmd("width",bodyWidth-lw)
                        break;
                        
                    default:
                    case "top":
                    
                        label.css({
                            left:p,
                            top:y+p,
                            width:bodyWidth
                        });
                        var lh = inx.height(label);
                        
                        if(lh)
                            y+= lh + 4;
                            
                        container.css({
                            top:y,
                        });
                        
                        item.cmd("width",bodyWidth);
                        y+= item.info("height");
                        
                        break;
                }            
                y += spacing;
                
            } else {
                container.css("display","none");
                label.css("display","none");
            }
            
        });
        
        this.cmd("setContentHeight",y-spacing); 

    }
}


/*-- /mod/bundles/inx/src/inx/base.js --*/


/*
    Base.js, version 1.1
    Copyright 2006-2007, Dean Edwards
    License: http://www.opensource.org/licenses/mit-license.php
*/

var Base = function() {
    // dummy
};

Base.extend = function(_instance, _static) { // subclass
    var extend = Base.prototype.extend;

    // build the prototype
    Base._prototyping = true;
    var proto = new this;
    extend.call(proto, _instance);
    delete Base._prototyping;

    // create the wrapper for the constructor function
    //var constructor = proto.constructor.valueOf(); //-dean
    var constructor = proto.constructor;
    var klass = proto.constructor = function() {
        if (!Base._prototyping) {            
        
            if(!this.private_id) { // - Golikov
                this.private_style = {};
                if(!arguments[0])
                    arguments[0] = {};            
                if(!arguments[0].listeners)
                    arguments[0].listeners = {};
                    
                this.private_id = arguments[0].id || inx.id();
                //this.private_id = inx.id(); // Генерируем id
                
                this.bj852tc92op9zqyli3f5 = true;
                inx.cmp.register(this.private_id,this);
            }
            
            if (this._constructing || this.constructor == klass) { // instantiation
                this._constructing = true;
                constructor.apply(this, arguments);
                delete this._constructing;
            } else if (arguments[0] != null) { // casting
                return (arguments[0].extend || extend).call(arguments[0], proto);
            }
        }
    };

    // build the class interface
    klass.ancestor = this;
    klass.extend = this.extend;
    klass.forEach = this.forEach;
    klass.implement = this.implement;
    klass.prototype = proto;
    klass.toString = this.toString;
    klass.valueOf = function(type) {
        //return (type == "object") ? klass : constructor; //-dean
        return (type == "object") ? klass : constructor.valueOf();
    };
    extend.call(klass, _static);
    // class initialisation
    if (typeof klass.init == "function") klass.init();
    return klass;
};

Base.prototype = {
    extend: function(source, value) {
        if (arguments.length > 1) { // extending with a name/value pair
            var ancestor = this[source];
            if (ancestor && (typeof value == "function") && // overriding a method?
                // the valueOf() comparison is to avoid circular references
                (!ancestor.valueOf || ancestor.valueOf() != value.valueOf()) &&
                /\bbase\b/.test(value)) {
                // get the underlying method
                var method = value.valueOf();
                // override
                value = function() {
                    var previous = this.base || Base.prototype.base;
                    this.base = ancestor;
                    var returnValue = method.apply(this, arguments);
                    this.base = previous;
                    return returnValue;
                };
                // point to the underlying method
                value.valueOf = function(type) {
                    return (type == "object") ? value : method;
                };
                value.toString = Base.toString;
            }
            this[source] = value;
        } else if (source) { // extending with an object literal
            var extend = Base.prototype.extend;
            // if this object has a customised extend method then use it
            if (!Base._prototyping && typeof this != "function") {
                extend = this.extend || extend;
            }
            var proto = {toSource: null};
            // do the "toString" and other methods manually
            var hidden = ["constructor", "toString", "valueOf"];
            // if we are prototyping then include the constructor
            var i = Base._prototyping ? 0 : 1;
            while (key = hidden[i++]) {
                if (source[key] != proto[key]) {
                    extend.call(this, key, source[key]);

                }
            }
            // copy each of the source object's properties to this object
            for (var key in source) {
                if (!proto[key]) extend.call(this, key, source[key]);
            }
        }
        return this;
    },

    base: function() {
        // call this method from any other method to invoke that method's ancestor
    }
};

// initialise
Base = Base.extend({
    constructor: function() {
        this.extend(arguments[0]);
    }
}, {
    ancestor: Object,
    version: "1.1",

    forEach: function(object, block, context) {
        for (var key in object) {
            if (this.prototype[key] === undefined) {
                block.call(context, object[key], key, object);
            }
        }
    },

    implement: function() {
        for (var i = 0; i < arguments.length; i++) {
            if (typeof arguments[i] == "function") {
                // if it's a function, call it
                arguments[i](this.prototype);
            } else {
                // add the interface using the extend method
                this.prototype.extend(arguments[i]);
            }
        }
        return this;
    },

    toString: function() {
        return String(this.valueOf());
    }
});


/*-- /mod/bundles/inx/src/inx/cmp.js --*/


inx.cmp = function(id) {

    this.ids = id instanceof Array ? id : [id];
    this.xyaz3m9ijf1hnw5zt890 = true; // Подпись inx.cmp

}

// Отправляет компоненту комаду
inx.cmp.prototype.cmd = function(name) {

    var ids = [];
    for(var i=0;i<this.ids.length;i++) ids.push(this.ids[i]);

    var ret;
    for(var i=0;i<ids.length;i++) {
        var cmp = inx.cmp.buffer[ids[i]];
        if(cmp) cmp = cmp.obj;
        if(cmp) ret = cmp.cmd.apply(cmp,arguments);
    }
    return ret===undefined ? this : ret;
}

// Создает задание для клмпонета
// От команды задание отличается отсутствием параметров,
// и тем что задание будет выполено не сразу а после завершения всех текущих операций
// Если добавить несколько одинаковых заданий подряд, то выполнится только одно
inx.cmp.prototype.task = function(name,time) {

    for(var i in this.ids) {

        var id = this.ids[i]
        var cmp = inx.cmp.buffer[id];
        if(cmp)
            cmp = cmp.obj;
        if(cmp)
            cmp.task(name,time);
    }

    return this;
}

inx.cmp.prototype.style = function(key,val) {
    var id = this.ids[0];
    var cmp = inx.cmp.buffer[id];
    if(cmp)
        cmp = cmp.obj;
    if(cmp)
        return cmp.style(key,val);
}

// Запрашивает какую-либо информацию об объекте
inx.cmp.prototype.info = function(name) {

    var ids = [];
    for(var i=0;i<this.ids.length;i++)
        ids.push(this.ids[i]);

    var ret;
    for(var i=0;i<ids.length;i++) {
        var cmp = inx.cmp.buffer[ids[i]];
        if(cmp)
            cmp = cmp.obj;
        if(cmp)
            return cmp.info.apply(cmp,arguments);
    }
    // return ret===undefined ? this : ret;
}

// Существует ли объект
inx.cmp.prototype.exists = function() {
    return !!inx.cmp.buffer[this.id()];
}

// Возвращает идентификатор объекта
inx.cmp.prototype.id = function() {
    return this.ids[0];
}

// Определяет хозяина объекта, напр. родительскую панель.
inx.cmp.prototype.setOwner = function(id) {
    id = inx(id).id();
    this.data("owner",id);
    return this;
}

// Возвращает родительский объект
inx.cmp.prototype.owner = function() {
    return inx(this.data("owner"));
}

// Возвращает цепочку родительских объектов
inx.cmp.prototype.owners = function() {
    var ret = [];
    var p = this;
    while(p.owner().exists()) {
        ret.push(p.owner().id())
        p = p.owner();
    }
    return inx(ret);
},

inx.cmp.prototype.axis = function(name) {
    var id = this.ids[0];
    name = "axis_"+name;
    var cmp = inx.cmp.buffer[id];
    if(cmp) cmp = cmp.obj;
    var a = []; for(var i=1;i<arguments.length;i++) a[i-1]=arguments[i];

    var ret = null;
    if(cmp && cmp[name] && typeof(cmp[name]=="function"))
        ret = cmp[name].apply(cmp,a);

    return inx(ret);
},

// Возвращает или устанавливает поле данных объекта
// Данные относятся привязываются к id объекта
inx.cmp.prototype.data = function(key,val) {

    if(arguments.length==1) {
        var b = inx.cmp.buffer[this.id()];
        if(b)
            b = b.data;
        if(b)
            return b[key];
    }

    if(arguments.length==2) {
        var b = inx.cmp.buffer[this.id()];
        if(b) {
            if(!b.data)b.data={};
            b.data[key] = val;
        }
        return this;
    }

}

// Подписывает на событие
inx.cmp.prototype.on = function(event,a,b) {
    var events = this.data("events");
    if(!events) events = {};
    this.data("events",events);
    if(!events[event]) events[event] = [];

    // this.on("event",function(){...})
    if(typeof(a)=="function") {
        events[event].push({fn:a});
        return this;
    }

    // this.on("event","alert(123)")
    if(typeof(a)=="string" && !!a.match(/\(/)) {
        events[event].push({fn:new Function(a)});
        return this;
    }

    // this.on("event",'method')
    if(typeof(a)=="string" && !a.match(/\(/) && !b) {
        events[event].push({id:this.id(),name:a});
        return this;
    }

    // this.on("event",'id','method')
    if(typeof(a)=="string" && typeof(b)=="string") {
        events[event].push({id:a,name:b});
        return this;
    }

    // this.on("event",['id','method'])
    if(typeof(a)=="object") {
        events[event].push({id:a[0],name:a[1]});
        return this;
    }

    inx.msg("inx.cmp.on - bad params",1);
}

// Вызывает событие
inx.cmp.prototype.fire = function(event,p1,p2,p3) {
    var obj = inx.cmp.buffer[this.id()];
    if(obj) obj = obj.obj;
    if(!obj) return;
    if(obj.__eventsDisabled) {
        return;
    }
    var events = this.data("events"); if(!events) return;
    events = events[event]; if(!events) return;
    var ret;
    var retFalse = false;
    for(var i in events) {
        var event = events[i];
        if(event.fn) {
            ret = event.fn.apply(obj,[p1,p2,p3]);
            retFalse = ret===false;
        } else {
            ret = inx(event.id).cmd(event.name,p1,p2,p3);
            retFalse = ret===false;
        }
    }

    if(retFalse)
        ret = false;

    return ret;
},

inx.cmp.prototype.bubble = function(event,p1,p2,p3) {
    var cmp = this;
    cmp.fire(event,p1,p2,p3);
    while(1) {
        cmp=cmp.owner();
        if(!cmp.exists())
            break;
        ret = cmp.fire(event,p1,p2,p3);
        if(ret===false)
            return;
    }
}

inx.cmp.prototype.suspendEvents = function() {
    var obj = inx.cmp.buffer[this.id()];
    if(obj) obj = obj.obj;
    if(!obj) return;
    obj.suspendEvents();
}

inx.cmp.prototype.unsuspendEvents = function() {
    var obj = inx.cmp.buffer[this.id()];
    if(obj) obj = obj.obj;
    if(!obj) return;
    obj.unsuspendEvents();
}

/**
 * Выполняет запрос к серверу
 **/
inx.cmp.prototype.call = function(p,success,error,meta) {

    var cmd = inx.cmp.create({
        type:"inx.command",
        data:p,
        meta:meta,
        source:this.id()
    });

    if(success)
        cmd.on("success",success);

    if(error)
        cmd.on("error",error);

    cmd.cmd("exec");

    return cmd.id();

}

// Глобальный вызов команд
inx.call = inx.cmp.prototype.call;

inx.cmp.prototype.here = function() {
    var id = this.ids[0];
    document.write("<div id='"+id+"' ></div>");

    var fn = function() {
        var id2 = $(inx(id).info("container")).attr("id");
        if(id2=="id")
            inx(id).cmd("width",$("#"+id).width());
    };

    this.cmd("render");
    this.cmd("appendTo","#"+id);
    this.on("componentLoaded",fn);

    $(window).resize(fn)
}

// -------------------------------------------------------------------------- Навигация

inx.cmp.prototype.items = function() {
    var items = this.info("param","private_items");
    return inx(items);
}

/**
 * Возвращает коллекцю потомков любого уровня для компонента
 **/
inx.cmp.prototype.allItems = function() {

    if(this.id()==0) {
        return inx(0);
    }

    var ret = [];
    this.items().each(function() {
        ret.push(this.id());        
        this.allItems().each(function(){
            ret.push(this.id());
        })        
    })
    return inx(ret);
}

inx.cmp.prototype.side = function() {
    var items = this.info("param","side");
    return inx(items);
}

inx.cmp.prototype.each = function(fn) {

    var ids = [];
    for(var i=0;i<this.ids.length;i++)
        ids.push(this.ids[i]);

    for(var i=0;i<ids.length;i++) {
        fn.apply(inx(ids[i]),[i]);
    }
}

inx.cmp.prototype.count = function() {
    return this.ids.length;
}

inx.cmp.prototype.eq = function(key,val) {
    var ids = [];
    for(var i=0;i<this.ids.length;i++)
        if(inx(this.ids[i]).info(key)==val)
            ids.push(this.ids[i]);
    return inx(ids);
}

inx.cmp.prototype.last = function() {
    var id = this.ids[this.ids.length-1];
    return inx(id);
}

inx.cmp.prototype.get = function(n) {
    var id = this.ids[n];
    return inx(id);
}

inx.cmp.prototype.neq = function(key,val) {
    var ids = [];
    for(var i=0;i<this.ids.length;i++)
        if(inx(this.ids[i]).info(key)!=val)
            ids.push(this.ids[i]);
    return inx(ids);
}

inx.cmp.prototype.length = function() {
    return this.ids.length;
}


// ----------------------------------------------------------------------- Статические функции

// Возвращает компонент по его дочернему элементу DOM
inx.cmp.fromElement = function(e) {
    e = $(e);
    while(e.length) {
        if(e.filter(".inx-box").data("id"))
            return inx(e.data("id"));
        e = e.parent();
    }
    return inx(0);
}

// Регистрирует компонент
inx.cmp.buffer = {};
inx.cmp.register = function(id,obj,rewrite) {
    var b = inx.cmp.buffer[id] || {};
    b.obj = obj;
    inx.cmp.buffer[id] = b;
}

inx.cmp.unregister = function(id) {
    setInterval(function() {
        delete inx.cmp.buffer[id];
    });
}

inx.cmp.__replace = function(id,cmp) {
    var obj = cmp.info("component");
    obj.private_id = id;
    inx.cmp.buffer[id] = obj;
}

// Создает компонент на основе массива параметров
inx.cmp.create = function(p) {
    if(p.xyaz3m9ijf1hnw5zt890) return p;
    var constructor;
    try {
        constructor = eval(p.type);
    } catch(ex) {}
    if(constructor)
        var cmp = new constructor(p);
    else
        var cmp = new inx.box.loader(p);
    return inx(cmp.id());
}


/*-- /mod/bundles/inx/src/inx/core.js --*/


// inline-block
inx.css(".inx-core-inlineBlock{display: -moz-inline-box;display: inline-table;display: inline-block;}");
inx.css(".inx-unselectable{-o-user-select: none;-webkit-user-select: none;-moz-user-select: -moz-none;-khtml-user-select: none;-ms-user-select: none;user-select: none;}");
inx.css(".inx-shadowframe{box-shadow: 0 0 30px rgba(0,0,0,.5);}");
inx.css(".inx-shadow{box-shadow: 0 0 30px rgba(0,0,0,.5);}");
inx.css(".inx-roundcorners{border-radius: 5px;-moz-border-radius: 5px; -webkit-border-radius: 5px;}");

inx.deselect = function() {
    if (window.getSelection) { window.getSelection().removeAllRanges(); }
    else if (document.selection && document.selection.empty)
        document.selection.empty();
}

$(document).mousedown(function(e){
    inx.mouseLButton = true;
    inx.__unselect = !!$(e.target).parents(".inx-unselectable").length;    
    inx.__text = !!$(e.target).parents().andSelf().filter("input,textarea").length; 
    if(inx.__text)
        inx.__unselect = false;
    if(inx.__unselect) {
        inx.deselect();
        e.preventDefault();
        window.focus();
    }
});

$(document).mouseup(function(e){
    inx.mouseLButton = false;
    inx.__unselect = false;
    var u = !!$(e.target).parents(".inx-unselectable").length;
    if(u && !inx.__text) {
        inx.deselect();      
        e.preventDefault();
    }
    inx.__text = false;
});

$(document).mousemove(function(e){
    if(inx.__unselect) {
        inx.deselect();      
        e.preventDefault(); 
    }    
});

/*-- /mod/bundles/inx/src/inx/core/focusManager.js --*/


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

/*-- /mod/bundles/inx/src/inx/core/observable.js --*/


inx.observable = Base.extend({

    constructor:function(p) {
    
        // Подписываеся на события
        this.listeners = {};        
        for(var i in p.listeners)
            this.on(i,p.listeners[i]);

        // Записываем параметры в свойства объекта
        // Если у объекта уже есть свойство, оно не перезаписывается
        for(var i in p) {
            if(this[i]===undefined) {
                this[i] = p[i];
            }
        }
        
        this.private_infoBuffer = {};
        
    },
    
    id:function() { return this.private_id; },
    
    cmd:function(name) {
    
        // Делаем чтобы рндер выполнялся только 1 раз
        if(name=="render") {
            if(this.private_z74gi3f1in) {
                return;            
            }
            this.private_z74gi3f1in = true;
        }    

        var a = [];
        for(var i=1;i<arguments.length;i++)
            a[i-1] = arguments[i];
            
        name = "cmd_"+name;
            
        var ret = null;
        
        if(inx.debug) {
            inx.observable.debug.stack.push(this.type+":"+name);
        }
        
        if(typeof(this[name])=="function") {
            try {
                var t1 = new Date().getTime();
                ret = this[name].apply(this,a);
                var t2 = new Date().getTime();
                var time = t2-t1;
                
                inx.observable.debug.cmdCountByName[name] = (inx.observable.debug.cmdCountByName[name] || 0) + 1;
                inx.observable.debug.totalTime[name] = (inx.observable.debug.totalTime[name] || 0) + time;
                
            } catch(ex) {
                ex.method = name;
                this.private_showError(ex);
            }
        }
            
        if(inx.debug) {
            inx.observable.debug.stack.pop();
        }
            
        return ret;
            
    },
    
    private_showError:function(ex) {
        var msg = "Error in " + this.id() + "<br/>";
        msg+= this.info("param","type")+":"+ex.method+"<br/>";
        msg+= ex.message;
        inx.msg(msg,1);  
    },
    
    task:function(name,time) {
        inx.taskManager.task(this.id(),name,time)
    },
    
    info:function(name) {
    
        if(inx.observable.buffer[name]) {
            if(this.private_infoBuffer[name] !== undefined) {
                return this.private_infoBuffer[name];
            }
        }
    
        var a = [];
        for(var i=1;i<arguments.length;i++) {
            a[i-1] = arguments[i];
        }
        
        var nn = name;
        name = "info_"+name;
        if(typeof(this[name])=="function")
            try {
            
                var t1 = new Date().getTime();
                var ret = this[name].apply(this,a);
                var t2 = new Date().getTime();
                var time = t2-t1;
                inx.observable.debug.totalTime[name] = (inx.observable.debug.totalTime[name] || 0) + time;
                this.private_infoBuffer[nn] = ret;
                //this.task("clearInfoBuffer");
                return ret;
                
            } catch(ex) {
                ex.method = name;
                this.private_showError(ex);
            }
               
    },    
    
    cmd_clearInfoBuffer:function() {
        this.private_infoBuffer = {};
        if(this.owner().id()) {
            this.owner().cmd("clearInfoBuffer");
        }
    },

    on:function(event,a,b) {
        inx(this.id()).on(event,a,b);
    },
    
    call:function(p,s,f,m) {
        return inx(this.id()).call(p,s,f,m);
    },
    
    suspendEvents:function() {
        this.__eventsDisabled=true
    },
    
    unsuspendEvents:function() {
        this.__eventsDisabled=false
    },

    fire:function() { 
        var cmp = inx(this.id());
        return cmp.fire.apply(cmp,arguments)
    },
    
    bubble:function(event,p1,p2,p3) {
        var cmp = inx(this.id());
        return cmp.bubble.apply(cmp,arguments)
    },
    
    cmd_destroy:function() {
        inx.cmp.unregister(this.id());
        this.fire("destroy");
    }

});

inx.observable.buffer = {
    width:true,
    height:true,
    innerHeight:true,
    contentWidth:true,
    scrollLeft:true
}

inx.observable.debug = {
    stack:[],
    cmd:0,
    cmds:{},
    cmdCountByID:{},
    cmdCountByName:{},
    totalTime:{},
}


/*-- /mod/bundles/inx/src/inx/core/observable/box.js --*/

 
inx.css(
    ".inx-box{background:none;font-family:Arial, Helvetica, sans-serif;font-size:12px;position:relative;overflow:hidden;color:black;cursor:default;white-space:normal;}"
);

inx.box = inx.observable.extend({

    constructor:function(p) {
    
        if(!p.style) {
            p.style = {}
        }
        
        if(!p.defaultStyle) {
            p.defaultStyle = {}
        }
    
        // Рекомендованя ширина компонента, опираясь на внутрений размер 
        if(!p.private_widthContent) {
            this.private_widthContent = 1;
        }
        
        // Рекомендованя ширина компонента, опираясь на внешний размер       
        if(!p.private_widthParent) 
            this.private_widthParent = 3;
        
        // Рекомендованя высота компонента, опираясь на внутрений размер  
        if(!p.private_heightContent)      
            this.private_heightContent = 1;
        
        // Рекомендованя высота компонента, опираясь на внешний размер
        if(!p.private_heightParent)
            this.private_heightParent = 1;
        
        this.private_hidden = !!p.hidden;
        
        this.private_styleChangedKeys = {};
        
        if(p.style) {          
            for(var i in p.style) {
                this.style(i,p.style[i]);
            }
        }
                
        if(p.width && !p.style.width) {
            this.style("width",p.width)
        }
            
        if(p.height && !p.style.height) {
            this.style("height",p.height)
        }  
        
        this.base(p);
                
    },
    
    /**
     * Возвращает ось родителей компонента
     **/
    axis_parents:function() {
    
        var ret = [];
        var cmp = inx(this);
        while(cmp.exists()) {
            ret.push(cmp.id());
            cmp = cmp.owner();
        }
        return ret;
    
    },
        
    cmd_mousewheel:function(deltha,e) {
        this.owner().cmd("mousewheel",deltha,e)
    },
    
    info_component:function() {
        return this;
    },
    
    info_type:function() {
        return this.type;
    },
    
    owner:function() {
        return inx(this.id()).owner();
    },
    
    items:function() {
        return inx(this).items();
    },
    
    info_region:function() {
        return this.region;
    },
    
    info_resizable:function() {
        return this.resizable;
    },
    
    info_rendered:function() {
        return !!this.private_z74gi3f1in;
    },
    
    info_title:function() {
        return this.title;
    },
    
    cmd_setTitle:function(title) {
        if(this.title==title) {
            return;
        }
        this.title = title+"";
        this.fire("titleChanged",this.title);
    },
    
    info_name:function() {
        return this.name;
    },
    
    cmd_destroy:function() {    
        this.base();
        // Устраиваем геноцид )
        this.items().cmd("destroy");
        // Удаляем себя из родителя )
        this.owner().cmd("remove",this);
        // Удаляем контейнер
        $(this.el).remove();
    },
    
    cmd_render:function() { 
    
        for(var i in this.defaultStyle) {
            if(this.private_style[i]===undefined) {
                this.style(i,this.defaultStyle[i]);
            }
        }
    
        this.el = $("<div class='inx-box' >");
        this.el.data("id",this.id());

        if(this.id()==inx.focusManager.cmp().id()) {
            this.cmd("handleFocusChange",true);
        }
        
        if(this.private_hidden) {
            this.cmd("hide");
        }    
        
        this.task("completeRender");
    },
    
    cmd_completeRender:function() {
        this.fire("render");
        this.cmd("updateStyle");
        this.private_layoutReady = true;
        inx.service("boxManager").outerSizeChanged(this.id());
    },
    
    info_layoutReady:function() {
    
        if(this.style("width")=="parent" && !this.private_widthParentSet) {
            return false;
        }
    
        return !!this.private_layoutReady;
    },
    
    cmd_appendTo:function(container) {
    
        if(!this.el) {
           return;
        }
           
        if(container) {
            this.container = $(container);
        }
            
        this.el.appendTo(this.container);
        this.task("syncLayout");

    },
    
    style:function(key,val) {       
  
        // Возврат результата
        if(val===undefined) {        
            var ret = this.private_style[key];
            if(ret===undefined) {
                return inx.box.defaultValues[key];     
            }
            return ret;
        }                
        
        // Запись результата
        var s = this.style(key);        
        if(s!=val) {
            this.private_style[key] = val; 
            this.private_styleChangedKeys[key] = true;
            this.cmd("clearInfoBuffer");
            this.task("updateStyle");
            inx.service("boxManager").innerSizeChanged(this.id());
        }        
        return this;
    },
    
    cmd_updateStyle:function() {
    
        if(!this.info("rendered")) {
            return;
        }
    
        for(var key in this.private_styleChangedKeys) {
        
            var val = this.private_style[key];
            switch(key) {

                case "background":
                    this.el.css("background",this.private_style["background"]);
                    break;
                    
                case "color":
                    this.el.css("color",this.private_style["color"]);
                    break;
                    
                case "borderRadius":
                    this.el.css("borderRadius",this.private_style["borderRadius"]);
                    break;
                    
                case "border":
                    this.el.css("border",(this.private_style["border"] ? 1 : 0 )+"px solid #cccccc");
                    this.cmd("updateBox");
                    break;
                    
                case "shadow":
                    this.el.css("box-shadow",this.private_style["shadow"] ? "0 0 10px rgba(0,0,0,.2)" : "none" );
                    break;
                    
                case "padding":
                case "spacing":
                case "maxHeight":
                case "vscroll":
                case "hscroll":
                case "autoWidth":
                case "titleMargin":
                case "valign":
                case "break":
                case "iconWidth":
                case "iconHeight":
                case "fontSize":
                case "iconAlign":
                    this.task("syncLayout");
                    break;                    
                    
                case "width":
                    this.cmd("updateBoxWidth");
                    break;
                    
                case "height":
                    this.cmd("updateBoxHeight");                    
                    break;
                
                default:
                    inx.msg("Unknown style "+key,1);
                    break;
            }
        }
        this.private_styleChangedKeys = {};
    },

    info_container:function() {
        return this.container;
    },
    
    /**
     * Устанавливает ширину панели (по родительскому компоненту)
     * Ширина расчитывается с учетом рамки
     **/
    cmd_width:function(width) {    
    
        width = parseInt(width);
        if(width<0) {
            width = 1;
        }
        
        this.private_widthParentSet = true;
            
        if(this.private_widthParent == width) {
            return;
        }
        
        this.private_widthParent = width;
        
        if(this.style("width")=="parent") {
            this.cmd("clearInfoBuffer");
            this.task("updateBoxWidth");   
            inx.service("boxManager").outerSizeChanged(this.id());
        }
    },
    
    /**
     * Устанавливает ширину панели.
     * Ширина расчитывается с учетом рамки
     **/
    cmd_widthContent:function(width) {    
    
        if(this.private_widthContent == width) {
            return;
        }
    
        this.cmd("clearInfoBuffer");
    
        this.private_widthContent = width;
        this.task("updateBoxWidth");   
        inx.service("boxManager").outerSizeChanged(this.id());
    },
    
    /**
     * Возвращает ширину панели с учетом рамки или 0 если панель скрыта.
     **/
    info_width:function() {
    
        if(this.private_hidden) {
            return 0;
        }
            
        var width = this.style("width");
        
        if(width=="parent") {
            width = this.private_widthParent;            
        }

        if(width=="content") {
            width = this.private_widthContent;
        }
                    
        return width*1;
    },
    
    cmd_updateBoxWidth:function() {
    
        var b = this.private_style.border ? 2 : 0;
        var width = this.info("width") - b;
        var hash = width + ":" + b;
        
        if(this.private_boxHashX!=hash) {
            if(this.el) {            
                this.el.css("width",width);                
            }
        }
        
        this.private_boxHashX = hash;
    },
    
    cmd_updateBoxHeight:function() {
    
        var b = this.private_style.border ? 2 : 0;
        var height = this.info("height") - b;
        var hash = height + ":" + b;
        if(this.private_boxHashY!=hash) {        
            if(this.el) {            
                this.el.css("height",height);  
                
                // Важно! размер бокса может измениться даже без изменения параметров компонента
                // К примеру, если доабвить панель в растягивающуюся панель, ее размер умеличится,
                // хотя никакой команды на смену размера не было
                // Поэтому нужна доп. проверка при изменении реальных размеров компонента              
                inx.service("boxManager").outerSizeChanged(this.id());
            }            
        }
        
        this.private_boxHashY = hash;
    },
    
    /**
     * Перерисовывает прямоугольник компонента: применяет ширину, высоту и бордер
     **/
    cmd_updateBox:function() {    
        this.cmd("updateBoxWidth");
        this.cmd("updateBoxHeight");
    },
    
    info_resizable:function() {
        return !!this.resizable
    },

    /**
     * Устанавливает высоту панели в режиме "По родителю"
     **/
    cmd_height:function(height) {  
    
        if(this.private_heightParent == height) {
            return;
        }
        
        this.cmd("clearInfoBuffer");
      
        this.private_heightParent = height;
        this.task("updateBoxHeight");
        inx.service("boxManager").outerSizeChanged(this.id());
    },
    
    /**
     * Устанавливает высоту панели в режиме "По контенту"
     **/
    cmd_setContentHeight:function(height) {

        if(this.private_heightContent == height) {
            return;
        }
        
        this.cmd("clearInfoBuffer");
        
        this.private_heightContent = height;
        this.task("updateBoxHeight");
        inx.service("boxManager").outerSizeChanged(this.id());
    },
    
    /**
     * Возвращает высоту контента
     **/         
    info_contentHeight:function() {
    
        if(this.private_heightContent instanceof Function) {
            this.private_heightContent = this.private_heightContent();    
        }
    
        return this.private_heightContent;
    },

    /**
     * Возвращает реальную высоту компонента
     **/
    info_height:function() {
    
        if(this.private_hidden) {
            return 0;
        }
            
        var height = this.style("height");
        
        if(height=="parent") {
            height = this.private_heightParent;            
        }

        if(height=="content") {
        
            height = this.info("contentHeight");
            
            height+= this.style("padding")*2;
            height+= this.private_style.border ? 2 : 0;
            height+= this.info("sideHeight") || 0;
            
            var maxh = this.style("maxHeight");
            if(maxh && height>maxh) {
                height = maxh;     
            }   
            
        }
                    
        return height*1;
    },

    cmd_border:function(b) {
        this.style("border",b);
    }, 
    
    info_border:function() {
        return this.style("border");
    },

    /**
     * Возвращает ширину панели без учета бордера
     **/
    info_innerWidth:function() {
        return this.info("width") - (this.private_style.border ? 2 : 0)
    },
    
    /**
     * Возвращает высоту панели без учета бордера
     **/
    info_innerHeight:function() {
        return this.info("height") - (this.private_style.border ? 2 : 0)
    },

    cmd_handleFocusChange:function(flag) {
        flag = !!flag;
        if(!this.el) return;
        this.el.css("borderColor",flag?"blue":"#cccccc");
        this.fire(flag?"focus":"blur");
        flag ? this.el.addClass("inx-focused") : this.el.removeClass("inx-focused")
    },
    
    cmd_handleSmoothBlur:function() {
        this.fire("smoothBlur");
    },

    /**
     * Устанавливает фокус на компонент
     **/
    cmd_focus:function() {

        // Если компонент не виден, не фокусируемся на нем    
        if(!this.info("visibleRecursive")) {
            return;
        }
    
        inx.focusManager.focus(this.id());
    },
    
    cmd_blur:function() {
        inx.focusManager.blur(this.id());
    },
    
    cmd_click:function(e) {
        this.fire("click",e);
    },

    cmd_dblclick:function(e) {
        this.fire("dblclick",e);
    },
    
    cmd_mousedown:function(e) {
        this.fire("mousedown",e);
    },
    
    cmd_mouseup:function(e) {
        this.fire("mouseup",e);
    },
    
    fire_key:function(e) {
        this.fire("keydown",e);
        return true;
    },

    /**
     * Показывает компонент
     **/    
    cmd_show:function() {

        if(this.el) {
            this.el.css("display","block");
        }
        
        this.cmd("clearInfoBuffer");
            
        this.fire("show");

        this.private_hidden = false;
        this.task("updateBox");
        inx.service("boxManager").outerSizeChanged(this.id());
        
    },
    
    cmd_hide:function() {
    
        if(this.el) {
            this.el.css("display","none");
        }
        
        this.cmd("clearInfoBuffer");
        this.fire("hide");
        this.private_hidden = true;
        inx.service("boxManager").outerSizeChanged(this.id());
        
    },
    
    cmd_toggle:function() {
        if(this.info("hidden")) {
            this.cmd("show");
        } else {
            this.cmd("hide");
        }
    },
    
    // true если компонент скрыт
    // false если компонент видимый
    info_hidden:function() {
        return !!this.private_hidden;
    },
    
    info_visible:function() {    
        return !this.private_hidden;
    },
    
    // Рекурсивно проверяет виден ли объект
    info_visibleRecursive:function() {
    
        // Если сам объект спрятан, возвращаем false
        if(this.info("hidden")) {
            return false;
        }
            
        visible = true;    
        inx(this).owners().each(function() {
            if(this.info("hidden"))
                visible = false;
        });
        return visible;
    },
    
    info_param:function(key) {
        return this[key];
    },
    
    cmd_syncLayout:function() {

    },
        
    info_help:function() {
        return this.help;
    },
   
    cmd_heightContentRaw:function(height) {     
        this.cmd("heightContent",height);
        this.cmd("clearInfoBuffer");
    },
    
    cmd_nativeUpdateLoader:function() {
        if(!inx.conf.ajaxIndicator) return false;
        var n = inx(this.id()).data("currentRequests");
        if(!n) {
            $(this.privateLoaderEl).remove();
            this.privateLoaderEl = null;
        }
        else {
            if(!this.privateLoaderEl) {
                this.privateLoaderEl = $("<div>").css({background:"white",position:"absolute",padding:5,zIndex:100});
                $("<img>").prop("src",inx.img("loader")).appendTo(this.privateLoaderEl)
            }
            this.privateLoaderEl.appendTo(this.el)
            if(this.__body) {
                var pos = this.__body.position();
                this.privateLoaderEl.css({top:pos.top,left:pos.left});
            }
        }
    }
        
}); 
inx.box.defaultValues = {
    width:"parent",
    height:"content",
    sidePriority:"v",
    padding:0,
    spacing:0,
    titleMargin:0,
    valign:"center",
    autoWidth:true,
    iconWidth:16,
    iconHeight:16,
    fontSize:12,
    iconAlign:"left",
    borderRadius:0,
    shadow:0,
    textColor:"black",
    vscroll:false,
    hscroll:false
}


/*-- /mod/bundles/inx/src/inx/core/observable/box/loader.js --*/


inx.box.loader = inx.box.extend({

    constructor:function(p) {
        this.initialParams = p;
        p.bypassAutoheight = true;
        this.base(p);
        inx.loader.load(p.type,this.id());
        this.private_cmdBuffer = [];
    },

    cmd_render:function() {
        this.base();
        this.el.html("<table style='width:100%;height:100%;'><tr><td style='text-align:center;'>"+inx.conf.componentLoadingHTML+"</td></tr></table>");
    },
    
    info_initialParams:function() {
        var p = this.initialParams;
        p.style = this.private_style;
        p.hidden = this.private_hidden;
        
        p.private_widthContent =  this.private_widthContent;
        p.private_widthParent =  this.private_widthParent;
        p.private_heightContent =  this.private_heightContent;
        p.private_heightParent =  this.private_heightParent;
        
        return p;
    },
    
    cmd:function(cmd,p1,p2,p3) {
        this.base(cmd,p1,p2,p3);
        if(!this["cmd_"+cmd])
            this.private_cmdBuffer.push([cmd,p1,p2,p3]);
    },
    
    cmd_handleLoad:function() {
    
        inx.taskManager.deleteTasks(this.id());
    
        var initialParams = this.info("initialParams");
        var container = this.info("container");
        var rendered = this.info("rendered");
    
        if(this.el)
            this.el.remove();
            
        var p = initialParams;
        p.id = this.id();
        
        // Обработчики событий не привязываются к данному объекту, поэтому второй раз
        // их регистрировать не нужно        
        p.listeners = [];
        var cmp = inx.cmp.create(p);                
        
        if(rendered) {
            cmp.cmd("render");    
            if(container)
                cmp.cmd("appendTo",container);
        }
            
        for(var i=0;i<this.private_cmdBuffer.length;i++) {
            var c = this.private_cmdBuffer[i];
            inx(this.id()).cmd(c[0],c[1],c[2],c[3]);
        }
        
        cmp.fire("componentLoaded");
    },
    
    info_loaderObj:function() {
        return true;
    }

});

/*-- /mod/bundles/inx/src/inx/core/observable/box/manager.js --*/


/* 
При изменении размеров окна будет запланирована проверка размеров
*/

inx.box.manager = new function() {

    var m = this;
    
    var that = this;
    this.__buffer = {};    
   
    this.innerSizeChanged = function(id) {
        if(!this.__buffer[id]) {
            this.__buffer[id] = {};
        }
        this.__buffer[id].inner = true;
        inx.box.manager.task.cmd("createTask");
    }
    
    this.outerSizeChanged = function(id) {
        if(!this.__buffer[id]) {
            this.__buffer[id] = {};
        }
        this.__buffer[id].outer = true;
        this.__buffer[id].inner = true;
        inx.box.manager.task.cmd("createTask");
    }
    
    /**
     * Выполняет задачу по проверке компонентов
     **/
    this.processBuffer = function() {
    
        m.timeout = null;
    
        var buffer = inx.deepCopy(m.__buffer);
        m.__buffer = {};
        
        for(var id in buffer) {
            var c = inx(id);
            if(buffer[id].inner) {
                if(c.info("rendered") && c.info("visible")) {
                    c.task("syncLayout");
                }
            }
            if(buffer[id].outer) {
                if(c.owner().info("rendered") && c.owner().info("visible")) {
                    c.owner().task("syncLayout");
                }
            }
        }

    }
    
    this.debug = function() {
        var ret = 0;
        for(var i in that.__buffer) {
            ret++;
        }
        return ret;
    }
    
    inx.service.register("boxManager",this);
    
}

inx.box.manager.task = inx({
    type:"inx.observable",
    cmd_createTask:function() {
        this.task("processBuffer");
    },
    cmd_processBuffer:function() {
        inx.box.manager.processBuffer();
    }
});

/*-- /mod/bundles/inx/src/inx/core/observable/command.js --*/

inx.command = inx.observable.extend({

    constructor:function(p) {
        if(!p.data) {
            p.data = {};
        }
        this.base(p);
    },
    
    cmd_logThis:function(param) {
    
        // Добавляем объект с заданным id в лог
        if(!inx.ajaxLog) {
            inx.ajaxLog = [];
        }
            
        var item;    
        for(var i=inx.ajaxLog.length-1;i>=0;i--) {
            if(inx.ajaxLog[i].id==this.id()) {
                item = inx.ajaxLog[i];
            }
        }
        
        if(!item) {
            item = {
                id:this.id()
            }
            inx.ajaxLog.push(item);
        }
        
        // Записываем в объект переданные параметры
        for(var i in param) {
            item[i] = param[i];
        }
            
    },
    
    cmd_exec:function() {    
    
        this.cmd("count",1);
    
        var id = this.id();
        
        files = false;
        if(window.File)
            for(var i in this.data)
                if(this.data[i] instanceof window.File)
                    files = true;

        var data;
        
        var p = {
            url:inx.conf.cmdUrl,
            type:"POST",
            success:function(d){                
                inx(id).cmd('handle',true,d).task("destroy");
            },error:function(r,status){
                if(status=="abort")
                    return;
                inx(id).cmd('handle',false,r.responseText).cmd("destroy");
            }
        };
        
        if(files) {
            p.data = new FormData();            
            for(var i in this.data) {
                p.data.append(i,this.data[i]);
            }   
            p.data.append("xcvb7c10q456ny1r74a6","xcvb7c10q456ny1r74a6");
            p.processData = false;
            p.contentType = false;
        } else {
            p.data = {
                data:JSON.stringify(this.data)
            }
        }

        this.request = $.ajax(p);
        this.cmd("logThis");
    },
    
    cmd_handle:function(success,response) {
    
        this.cmd("count",-1);
    
        // Если сам запрос к серверу не увенчался успехом
        if(!success) {
            inx.msg(response,1);
            this.fire("error");
            this.cmd("logThis",{statusText:"Server error"});
            return;
        }
        
        // Пробуем разобрать ответ от сервера
        var ret = inx.command.parse(response);
        
        // Показываем сообщения
        if(ret.messages) {
            for(var i=0;i<ret.messages.length;i++) {
                var msg = ret.messages[i];
                inx.msg(msg.text,msg.error);
            }
        }
        
        // При ошибке разбора показываем уведомление
        if(!ret.success) {
            if(ret.text) inx.msg(ret.text,1);
            this.fire("error");
            this.cmd("logThis",{statusText:"Parse JSON error"});
            return;
        }
        
        if(ret.events) {
            for(var i=0;i<ret.events.length;i++) {
                var event = ret.events[i];
                inx.fire(event.name,event.params)
            }
        }
        
        // Если мы дошли до этого места, значит запрос успешен
        // Дополняем мету локальными ключами
        if(this.meta) {
            if(!ret.meta)
                ret.meta = {};
            for(var i in this.meta) {
                ret.meta[i] = this.meta[i];
            }
        }

        this.fire("success",ret.data,ret.meta);
        
        this.cmd("logThis",{
            statusText:"Success",
            profiler:ret.profiler,
        });
        
        inx.taskManager.exec();
        this.cmd("logThis");
    },
    
    cmd_count:function(p) {
        if(this.lastCount==p)
            return;
        this.lastCount = p;
        var cmp = inx(this.source);
        var n = (cmp.data("currentRequests") || 0)+p;
        cmp.data("currentRequests",n);
        cmp.cmd("nativeUpdateLoader",n);
    },
    
    cmd_destroy:function() {
        try {
            this.cmd("count",-1);
            this.request.abort();
        } catch(ex) { }
        this.base();
    }

});

// разбирает строку json с ответом от сервера.
// возвращает объект:
// {success:true/false,data:...,meta:...}
// В случае успеха выводит сообщения
// При ошибке разбора выводит ответ от сервера как сообщение
inx.command.parse = function(str) {

    try{
        eval("var data="+str);
    } catch(ex) {
        return {success:false,text:str};
    } 
    
    return {
        success:data.completed,
        messages:data.messages,
        data:data.data,
        meta:data.meta,
        events:data.events
    }
}


/*-- /mod/bundles/inx/src/inx/events.js --*/


/**
 * Реализация подписки и вызова глобальных событий
 **/
inx.events = new function() {

    var that = this;
    
    var handlers = [];
    var globalHandlers = [];

    this.on = function(name,handler) {
        if(!handlers[name]) {
            handlers[name] = [];
        }
        handlers[name].push(handler);
    }
    
    this.global = function(handler) {
        globalHandlers.push(handler);
    }
    
    /**
     * Вызывает глобальное событие name
     **/
    this.fire = function(name,p1,p2,p3) {
        var h = handlers[name];
        that.processHandlersArray(globalHandlers,name,p1,p2,p3);
        return that.processHandlersArray(h,p1,p2,p3);                
    }
    
    /**
    * Выполняет массив коллбэков
    * Выбрасывает из него ссылки на уже не существующие объекты
    * Если хоть один из кэлбэков вернул false, возвращает false
    **/
    this.processHandlersArray = function(handlers,p1,p2,p3) {
    
        if(!handlers) {
            return;
        }
    
        var retFalse = false;
        var ret = null;        
        
        for(var i in handlers) {
        
            var handler = handlers[i];
            
            if(handler instanceof Function) {
            
                ret = handler(p1,p2,p3);
                retFalse = ret===false;
                
            } else {
            
                var cmp = inx(handler[0]);
 
                var extraParams = handler[2];
                if(extraParams) {
                    if(extraParams.visibleOnly && !cmp.info("visibleRecursive")) {
                        continue;    
                    }
                }
                
                ret = cmp.cmd(handler[1],p1,p2,p3);
                retFalse = ret===false;
            }
            
        }
        
        if(retFalse) {
            ret = false;
        }
        
        return ret;
    }
    
    inx.service.register("events",this);

}


/*-- /mod/bundles/inx/src/inx/help.js --*/


inx.css(".a2op9st4e2jw {font-size:11px;background:#ffeebb;max-width:200px;position:absolute;padding:10px;}");

inx.help = {

    show:function(id,x,y) {
        var help = inx(id).info("param","help");
        if(!help) return;
        if(!inx.help.e)
            inx.help.e = $("<div>")
                .addClass("a2op9st4e2jw")
                .addClass("inx-shadowframe")
                .appendTo("body")
                .css({zIndex:inx.conf.z_index_message})
        inx.help.e.fadeIn(300).css({left:x+10,top:y+10}).html(help+"");
    },

    hide:function() {
        if(inx.help.e)
            inx.help.e.hide();
    }

}


/*-- /mod/bundles/inx/src/inx/hotkey.js --*/


inx.hotkey = {}

inx.hotkey = function(key,handler) {

    if(handler instanceof Array) {
    
        handler.push({
            visibleOnly:true
        });
    
    }

    inx.service("key").on(key,handler);
}



/*-- /mod/bundles/inx/src/inx/img.js --*/


/**
 * Возвращает адрес картинки
 * Вартианты:
 * plus => .../img/plus.gif
 * %img%/myfilder/plus.png => .../img/myfolder/plus.png
 **/
inx.img = function(name) {

    if(!name) {
        return false;
    }
    
    if((name+"").match(/^[\w-_]+$/)) {
        return inx.path("%res%/img/icons16/"+name+".gif");
    }
        
    return name;
}


/*-- /mod/bundles/inx/src/inx/json.js --*/

inx.json = {

    encode:function(data) {
        return JSON.stringify(data);
    },
    
    decode:function(str) {
        try{ eval("var data="+str);return data; }
        catch(ex){ return null; }
    }

}

/*
    http://www.inx.JSON.org/json2.js
    2008-09-01

    Public Domain.

    NO WARRANTY EXPRESSED OR IMPLIED. USE AT YOUR OWN RISK.

    See http://www.inx.JSON.org/js.html

    This file creates a global JSON object containing two methods: stringify
    and parse.

        JSON.stringify(value, replacer, space)
            value       any JavaScript value, usually an object or array.

            replacer    an optional parameter that determines how object
                        values are stringified for objects. It can be a
                        function or an array of strings.

            space       an optional parameter that specifies the indentation
                        of nested structures. If it is omitted, the text will
                        be packed without extra whitespace. If it is a number,
                        it will specify the number of spaces to indent at each
                        level. If it is a string (such as '\t' or '&nbsp;'),
                        it contains the characters used to indent at each level.

            This method produces a JSON text from a JavaScript value.

            When an object value is found, if the object contains a toJSON
            method, its toJSON method will be called and the result will be
            stringified. A toJSON method does not serialize: it returns the
            value represented by the name/value pair that should be serialized,
            or undefined if nothing should be serialized. The toJSON method
            will be passed the key associated with the value, and this will be
            bound to the object holding the key.

            For example, this would serialize Dates as ISO strings.

                Date.prototype.toJSON = function (key) {
                    function f(n) {
                        // Format integers to have at least two digits.
                        return n < 10 ? '0' + n : n;
                    }

                    return this.getUTCFullYear()   + '-' +
                         f(this.getUTCMonth() + 1) + '-' +
                         f(this.getUTCDate())      + 'T' +
                         f(this.getUTCHours())     + ':' +
                         f(this.getUTCMinutes())   + ':' +
                         f(this.getUTCSeconds())   + 'Z';
                };

            You can provide an optional replacer method. It will be passed the
            key and value of each member, with this bound to the containing
            object. The value that is returned from your method will be
            serialized. If your method returns undefined, then the member will
            be excluded from the serialization.

            If the replacer parameter is an array of strings, then it will be used to
            select the members to be serialized. It filters the results such
            that only members with keys listed in the replacer array are
            stringified.

            Values that do not have JSON representations, such as undefined or
            functions, will not be serialized. Such values in objects will be
            dropped; in arrays they will be replaced with null. You can use
            a replacer function to replace those with JSON values.
            JSON.stringify(undefined) returns undefined.

            The optional space parameter produces a stringification of the
            value that is filled with line breaks and indentation to make it
            easier to read.

            If the space parameter is a non-empty string, then that string will
            be used for indentation. If the space parameter is a number, then
            the indentation will be that many spaces.

            Example:

            text = JSON.stringify(['e', {pluribus: 'unum'}]);
            // text is '["e",{"pluribus":"unum"}]'


            text = JSON.stringify(['e', {pluribus: 'unum'}], null, '\t');
            // text is '[\n\t"e",\n\t{\n\t\t"pluribus": "unum"\n\t}\n]'

            text = JSON.stringify([new Date()], function (key, value) {
                return this[key] instanceof Date ?
                    'Date(' + this[key] + ')' : value;
            });
            // text is '["Date(---current time---)"]'


        JSON.parse(text, reviver)
            This method parses a JSON text to produce an object or array.
            It can throw a SyntaxError exception.

            The optional reviver parameter is a function that can filter and
            transform the results. It receives each of the keys and values,
            and its return value is used instead of the original value.
            If it returns what it received, then the structure is not modified.
            If it returns undefined then the member is deleted.

            Example:

            // Parse the text. Values that look like ISO date strings will
            // be converted to Date objects.

            myData = JSON.parse(text, function (key, value) {
                var a;
                if (typeof value === 'string') {
                    a =
/^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2}(?:\.\d*)?)Z$/.exec(value);
                    if (a) {
                        return new Date(Date.UTC(+a[1], +a[2] - 1, +a[3], +a[4],
                            +a[5], +a[6]));
                    }
                }
                return value;
            });

            myData = JSON.parse('["Date(09/09/2001)"]', function (key, value) {
                var d;
                if (typeof value === 'string' &&
                        value.slice(0, 5) === 'Date(' &&
                        value.slice(-1) === ')') {
                    d = new Date(value.slice(5, -1));
                    if (d) {
                        return d;
                    }
                }
                return value;
            });


    This is a reference implementation. You are free to copy, modify, or
    redistribute.

    This code should be minified before deployment.
    See http://javascript.crockford.com/jsmin.html

    USE YOUR OWN COPY. IT IS EXTREMELY UNWISE TO LOAD CODE FROM SERVERS YOU DO
    NOT CONTROL.
*/

/*jslint evil: true */

/*global JSON */

/*members "", "\b", "\t", "\n", "\f", "\r", "\"", JSON, "\\", call,
    charCodeAt, getUTCDate, getUTCFullYear, getUTCHours, getUTCMinutes,
    getUTCMonth, getUTCSeconds, hasOwnProperty, join, lastIndex, length,
    parse, propertyIsEnumerable, prototype, push, replace, slice, stringify,
    test, toJSON, toString, valueOf
*/

// Create a JSON object only if one does not already exist. We create the
// methods in a closure to avoid creating global variables.

if (!inx.JSON) {
    JSON = {};
}
(function () {

    function f(n) {
        // Format integers to have at least two digits.
        return n < 10 ? '0' + n : n;
    }

    if (typeof Date.prototype.toJSON !== 'function') {

        Date.prototype.toJSON = function (key) {

            return this.getUTCFullYear()   + '-' +
                 f(this.getUTCMonth() + 1) + '-' +
                 f(this.getUTCDate())      + 'T' +
                 f(this.getUTCHours())     + ':' +
                 f(this.getUTCMinutes())   + ':' +
                 f(this.getUTCSeconds())   + 'Z';
        };

        String.prototype.toJSON =
        Number.prototype.toJSON =
        Boolean.prototype.toJSON = function (key) {
            return this.valueOf();
        };
    }

    var cx = /[\u0000\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,
        escapeable = /[\\\"\x00-\x1f\x7f-\x9f\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,
        gap,
        indent,
        meta = {    // table of character substitutions
            '\b': '\\b',
            '\t': '\\t',
            '\n': '\\n',
            '\f': '\\f',
            '\r': '\\r',
            '"' : '\\"',
            '\\': '\\\\'
        },
        rep;


    function quote(string) {

// If the string contains no control characters, no quote characters, and no
// backslash characters, then we can safely slap some quotes around it.
// Otherwise we must also replace the offending characters with safe escape
// sequences.

        escapeable.lastIndex = 0;
        return escapeable.test(string) ?
            '"' + string.replace(escapeable, function (a) {
                var c = meta[a];
                if (typeof c === 'string') {
                    return c;
                }
                return '\\u' + ('0000' + a.charCodeAt(0).toString(16)).slice(-4);
            }) + '"' :
            '"' + string + '"';
    }


    function str(key, holder) {

// Produce a string from holder[key].

        var i,          // The loop counter.
            k,          // The member key.
            v,          // The member value.
            length,
            mind = gap,
            partial,
            value = holder[key];

// If the value has a toJSON method, call it to obtain a replacement value.

        if (value && typeof value === 'object' &&
                typeof value.toJSON === 'function') {
            value = value.toJSON(key);
        }

// If we were called with a replacer function, then call the replacer to
// obtain a replacement value.

        if (typeof rep === 'function') {
            value = rep.call(holder, key, value);
        }

// What happens next depends on the value's type.

        switch (typeof value) {
        case 'string':
            return quote(value);

        case 'number':

// JSON numbers must be finite. Encode non-finite numbers as null.

            return isFinite(value) ? String(value) : 'null';

        case 'boolean':
        case 'null':

// If the value is a boolean or null, convert it to a string. Note:
// typeof null does not produce 'null'. The case is included here in
// the remote chance that this gets fixed someday.

            return String(value);

// If the type is 'object', we might be dealing with an object or an array or
// null.

        case 'object':

// Due to a specification blunder in ECMAScript, typeof null is 'object',
// so watch out for that case.

            if (!value) {
                return 'null';
            }

// Make an array to hold the partial results of stringifying this object value.

            gap += indent;
            partial = [];

// If the object has a dontEnum length property, we'll treat it as an array.

            if (typeof value.length === 'number' &&
                    !value.propertyIsEnumerable('length')) {

// The object is an array. Stringify every element. Use null as a placeholder
// for non-JSON values.

                length = value.length;
                for (i = 0; i < length; i += 1) {
                    partial[i] = str(i, value) || 'null';
                }

// Join all of the elements together, separated with commas, and wrap them in
// brackets.

                v = partial.length === 0 ? '[]' :
                    gap ? '[\n' + gap +
                            partial.join(',\n' + gap) + '\n' +
                                mind + ']' :
                          '[' + partial.join(',') + ']';
                gap = mind;
                return v;
            }

// If the replacer is an array, use it to select the members to be stringified.

            if (rep && typeof rep === 'object') {
                length = rep.length;
                for (i = 0; i < length; i += 1) {
                    k = rep[i];
                    if (typeof k === 'string') {
                        v = str(k, value);
                        if (v) {
                            partial.push(quote(k) + (gap ? ': ' : ':') + v);
                        }
                    }
                }
            } else {

// Otherwise, iterate through all of the keys in the object.

                for (k in value) {
                    if (Object.hasOwnProperty.call(value, k)) {
                        v = str(k, value);
                        if (v) {
                            partial.push(quote(k) + (gap ? ': ' : ':') + v);
                        }
                    }
                }
            }

// Join all of the member texts together, separated with commas,
// and wrap them in braces.

            v = partial.length === 0 ? '{}' :
                gap ? '{\n' + gap + partial.join(',\n' + gap) + '\n' +
                        mind + '}' : '{' + partial.join(',') + '}';
            gap = mind;
            return v;
        }
    }

// If the JSON object does not yet have a stringify method, give it one.

    if (typeof JSON.stringify !== 'function') {
        JSON.stringify = function (value, replacer, space) {

// The stringify method takes a value and an optional replacer, and an optional
// space parameter, and returns a JSON text. The replacer can be a function
// that can replace values, or an array of strings that will select the keys.
// A default replacer method can be provided. Use of the space parameter can
// produce text that is more easily readable.

            var i;
            gap = '';
            indent = '';

// If the space parameter is a number, make an indent string containing that
// many spaces.

            if (typeof space === 'number') {
                for (i = 0; i < space; i += 1) {
                    indent += ' ';
                }

// If the space parameter is a string, it will be used as the indent string.

            } else if (typeof space === 'string') {
                indent = space;
            }

// If there is a replacer, it must be a function or an array.
// Otherwise, throw an error.

            rep = replacer;
            if (replacer && typeof replacer !== 'function' &&
                    (typeof replacer !== 'object' ||
                     typeof replacer.length !== 'number')) {
                throw new Error('JSON.stringify');
            }

// Make a fake root object containing our value under the key of ''.
// Return the result of stringifying the value.

            return str('', {'': value});
        };
    }


// If the JSON object does not yet have a parse method, give it one.

    if (typeof JSON.parse !== 'function') {
        JSON.parse = function (text, reviver) {

// The parse method takes a text and an optional reviver function, and returns
// a JavaScript value if the text is a valid JSON text.

            var j;

            function walk(holder, key) {

// The walk method is used to recursively walk the resulting structure so
// that modifications can be made.

                var k, v, value = holder[key];
                if (value && typeof value === 'object') {
                    for (k in value) {
                        if (Object.hasOwnProperty.call(value, k)) {
                            v = walk(value, k);
                            if (v !== undefined) {
                                value[k] = v;
                            } else {
                                delete value[k];
                            }
                        }
                    }
                }
                return reviver.call(holder, key, value);
            }


// Parsing happens in four stages. In the first stage, we replace certain
// Unicode characters with escape sequences. JavaScript handles many characters
// incorrectly, either silently deleting them, or treating them as line endings.

            cx.lastIndex = 0;
            if (cx.test(text)) {
                text = text.replace(cx, function (a) {
                    return '\\u' +
                        ('0000' + a.charCodeAt(0).toString(16)).slice(-4);
                });
            }

// In the second stage, we run the text against regular expressions that look
// for non-JSON patterns. We are especially concerned with '()' and 'new'
// because they can cause invocation, and '=' because it can cause mutation.
// But just to be safe, we want to reject all unexpected forms.

// We split the second stage into 4 regexp operations in order to work around
// crippling inefficiencies in IE's and Safari's regexp engines. First we
// replace the JSON backslash pairs with '@' (a non-JSON character). Second, we
// replace all simple value tokens with ']' characters. Third, we delete all
// open brackets that follow a colon or comma or that begin the text. Finally,
// we look to see that the remaining characters are only whitespace or ']' or
// ',' or ':' or '{' or '}'. If that is so, then the text is safe for eval.

            if (/^[\],:{}\s]*$/.
test(text.replace(/\\(?:["\\\/bfnrt]|u[0-9a-fA-F]{4})/g, '@').
replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, ']').
replace(/(?:^|:|,)(?:\s*\[)+/g, ''))) {

// In the third stage we use the eval function to compile the text into a
// JavaScript structure. The '{' operator is subject to a syntactic ambiguity
// in JavaScript: it can begin a block or an object literal. We wrap the text
// in parens to eliminate the ambiguity.

                j = eval('(' + text + ')');

// In the optional fourth stage, we recursively walk the new structure, passing
// each name/value pair to a reviver function for possible transformation.

                return typeof reviver === 'function' ?
                    walk({'': j}, '') : j;
            }

// If the text is not JSON parseable, then a SyntaxError is thrown.

            throw new SyntaxError('JSON.parse');
        };
    }
})();


/*-- /mod/bundles/inx/src/inx/keyManager.js --*/


$(function() {
    setTimeout(function() {
    
        // Устанавливаем обработчик keydown
        inx.service("key").on("keydown",function(e) {

            var cmp = inx.focusManager.cmp();
            while(cmp.exists()) {
                var ret = cmp.cmd("keydown",e);
                if(ret===false) {
                    e.preventDefault();
                    break;
                } else if(ret=="stop") {
                    break;
                }
                cmp = cmp.owner();
            }

        });

        // Устанавливаем обработчик keypress
        inx.service("key").on("keypress",function(e) {

            var cmp = inx.focusManager.cmp();
            var str = String.fromCharCode(e.which);

            var ret = cmp.cmd("keypress",str);
            if(ret==false) {
                e.preventDefault();
            }

        });

    })
});

/*-- /mod/bundles/inx/src/inx/loader.js --*/


inx.loader = {

    heap:[],
    code:[],
    count:0,
    handlers:[],
    dependency:[],
    ready:{},
    
    is_requested:function(name) {
        for(var i=0;i<this.heap.length;i++)
            if(this.heap[i]==name)
                return true;
        return false;
    },    

    load:function(name,handler) {
    
        if(handler) inx.loader.handlers.push(handler);
    
        // Определяем путь
        var path = name.split(".");
        path = inx.conf.url+path.join("/")+".js";
        //path+='?_='+inx.conf.build_id;
        
        if(inx.loader.is_requested(name)) return;

        inx.loader.heap.unshift(name);
        inx.loader.count++;        
    
        $.ajax({
            type: "GET",
            url: path,
            cache:true,
            dataType:"html",
            success:function(data){
            
                var include = (data.split("\n")[0]+"").match(/\/\/[ ]*@include(.*)/);
                include = include ? include[1].split(",") : [];
                for(var i in include)
                    inx.loader.load($.trim(include[i]));
                inx.loader.count--;
                inx.loader.code[name] = data;

                inx.loader.dependency[name] = include;
                
                if(inx.loader.count==0)
                    inx.loader.exec();
            },
            error:function() {
                inx.msg("Script loading error ("+name+")",1)
            }
        });        
    },
    
    exec:function() {
    
        // Дублируем массив обработчиков
        // Т.к. в процесе выполнения могут быть объявлены новые обработчики,
        // то они должны быть добавлены в новый массив
        var handlers = [];
        for(var i=0;i<inx.loader.handlers.length;i++)
            handlers.push(inx.loader.handlers[i]);
        this.handlers = [];
        
        for(var i=0;i<inx.loader.heap.length;i++)
            inx.loader.eval(inx.loader.heap[i]);
        for(var i=0;i<handlers.length;i++)
            inx(handlers[i]).cmd("handleLoad");
    },
    
    eval:function(name) {

        name = $.trim(name);
        if(!inx.loader.code[name]) return;

        for(var i=0;i<inx.loader.dependency[name].length;i++)
            inx.loader.eval(inx.loader.dependency[name][i]);

        try {
            eval(inx.loader.code[name]);
        }
        catch(e) {
            inx.msg("error while eval "+name,1);
            inx.msg(e,1);
        }
        inx.loader.code[name] = null;
        inx.loader.ready[name] = true;
    },
    
    debug:function() {
        var r = [];
        for(var i in inx.loader.ready)
            r.push(i);
        return r.join("<br/>");
    }
}


/*-- /mod/bundles/inx/src/inx/msg.js --*/

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




/*-- /mod/bundles/inx/src/inx/sizeObserver.js --*/


inx.sizeObserver = new function() {

    var buffer = [];
    
    var checkElement = function(e) {
        
        var height = inx.height(e);
        if(height != e.data("lastHeight")) {                
            e.data("lastHeight",height);
            var id = e.data("height-id");
            var fn = e.data("height-fn");
            inx(id).cmd(fn,height);
        }
    }

    this.add = function(e,id,fn) {
    
        e = $(e);
        if(e.data("x4lsncvi7")) {
            return;
        }
        
        e.data("x4lsncvi7",true);
        e.data("height-id",id);
        e.data("height-fn",fn);
        e.addClass("x4lsncvi7");
        buffer.push(e);
        checkElement(e);
    
    }
    
    var checkBuffer = function() {
    
        $(".x4lsncvi7:visible").each(function() {
            var e = $(this);
            checkElement(e);
        });
        
    }
    
    setInterval(checkBuffer,200);

}

/*-- /mod/bundles/inx/src/inx/storage.js --*/


inx.storage = {

    buffer:{},

    set:function(key,val) {       
        inx.storage.buffer[key] = val;
        if(inx.storage.ready & !inx.storage.dumpPlanned) {
            setTimeout(function(){
                inx.storage.flush();
            },300);
            inx.storage.dumpPlanned = true;            
        }
    },
    
    flush:function() {
        for(var key in inx.storage.buffer) {
            var val = inx.storage.buffer[key];
            key = inx.storage.hash(key);
            try { Storage.put(key,inx.json.encode(val)); }
            catch(ex) { inx.msg("storage error",1); inx.msg(ex,1); }
        }
        inx.storage.dumpPlanned = false;        
        inx.storage.buffer = {};
    },
    
    get:function(key) {
        try {
            var ret = inx.storage.buffer[key];
            if(ret!==undefined) return ret;
            if(!inx.storage.ready) return null;
            key = inx.storage.hash(key);
            return inx.json.decode(Storage.get(key));
        } catch(ex) {
            inx.msg("inx.storage.get",1);
            inx.msg(ex,1);
        }
    },
    
    keys:function() {
        if(!inx.storage.ready) return [];
        inx.storage.flush();
        return Storage.getKeys();
    },
    
    onready:function(id,cmd) {
        id = inx(id).id();
        if(inx.storage.ready) {
            inx(id).cmd(cmd);
            return;
        }
        inx.storage.h.push({id:id,cmd:cmd});
    },
    
    h:[],
    
    private_init:function() {
        inx.storage.ready = true;    
        for(var i=0;i<inx.storage.h.length;i++)
            inx(inx.storage.h[i].id).cmd(inx.storage.h[i].cmd);
        inx.storage.flush();
    },
    
    hash:function(key) {
        key+="";
        var ret = "";
        for(var i=0;i<key.length;i++)
            ret+= "x"+key.charCodeAt(i);
        return key.replace(/\.|\:/g,"_");
    }
},

/**
 * (c) 2008, Ilya Kantor
 * 1.1
 * http://browserpersistence.ru - Последняя версия и документация
 * Разработка спонсирована компанией Интернет-Обновление
 * http://obnovlenie.ru
 *
 * Вы можете делать с этим кодом, что хотите, но оставьте эти строки
 * И, пожалуйста, сообщайте об ошибках и полезных дополнениях на http://browserpersistence.ru
 * 
 */


/**
 * Использование:
 *
 * Внутри document.body, или после onDOMContentLoaded (чтобы было body):
 *   Storage.init(function() { .. Каллбэк после успешной загрузки .. }
*/
Storage = {
    
    // Flash8 загружается последним и асинхронно, остальные - синхронно
    engines: ["WhatWG", "userData", "Flash8"],
    //"userData",
    
    swfUrl: inx.path("%res%/swf/storage.swf"),
        
    init: function(onready) {       
        for(var i=0; i<this.engines.length; i++) {                    
                    
            try {
                this[this.engines[i]](function() { Storage.active = true; onready && onready()})
                return;                       
            } catch(e) {
                // uncomment to see errors                
                //alert(this.engines[i]+':<'+e.message+'>\n')
                //inx.msg((this.engines[i]+':<'+e.message+'>\n'));
            }        
        }
        inx.msg("No storage found",1);
        //inx.msg(i);         
        
    }
    
}

    

Storage.WhatWG = function(onready) {
    
    var storage = globalStorage[location.hostname];
            
    Storage = {
    
        put: function(key, value) {
            storage[key] = value
        },
        
        get: function(key) {
            return String(storage[key])
        },
        
        remove: function(key) {
            delete storage[key]
        },    
        
        getKeys: function() {
            var list = []
            
            for(i in storage) list.push(i)
            
            return list
        },
        
        clear: function() {
            for(i in storage) {
                delete storage[i]
            }
        }     
    }
    
    onready()
}




Storage.userData = function(onready) {
    var namespace = "data"    

    if (!document.body.addBehavior) {            
        throw new Error("No addBehavior available")
    }
        
    var storage = document.getElementById('storageElement');
    if (!storage) {
        storage = document.createElement('span')
        document.body.appendChild(storage)
        storage.addBehavior("#default#userData");
        storage.load(namespace);
    } 
    
    Storage = {
        put: function(key, value) {
            storage.setAttribute(key, value)
            storage.save(namespace)
        },
        
        get: function(key) {
            return storage.getAttribute(key)
        },
        
        remove: function(key) {
            storage.removeAttribute(key)
            storage.save(namespace)
        },
        
        getKeys: function() {
            var list = []
            var attrs = storage.XMLDocument.documentElement.attributes
            
            for(var i=0; i<attrs.length; i++) {
                list.push(attrs[i].name)
            }
            
            return list
        },
        
        clear: function() {
            var attrs = storage.XMLDocument.documentElement.attributes
            
            for(var i=0; i<attrs.length; i++) {
                storage.removeAttribute(attrs[i].name)
            }
            storage.save(namespace)
        }
    }
    
    onready();
}


Storage.Flash8 = function(onready) { 
    
    var movie
        
    var swfId = "StorageMovie"
    while(document.getElementById(swfId)) swfId = '_'+swfId
    
    var swfUrl = Storage.swfUrl
    
    // first setup storage, make it ready to accept back async call
    Storage = {       
 
        put: function(key, value) {
            movie.put(key, value)        
        },
        
        get: function(key) {
            return movie.get(key)
        },
        
        remove: function(key) {
            movie.remove(key)
        },
        
        getKeys: function() {
            return movie.getkeys()  // lower case in flash to evade ExternalInterface bug         
        },
        
        clear: function() {
            movie.clear()
        },
        
        ready: function() {
            movie = document[swfId]
            onready();
        }
    }
    
    // now write flash into document
    
    var protocol = window.location.protocol == 'https' ? 'https' : 'http'

    var containerStyle = "width:0; height:0; position: absolute; z-index: 10000; top: -1000px; left: -1000px;"        

    var objectHTML = '<embed src="' + swfUrl + '" '
                              + ' bgcolor="#ffffff" width="0" height="0" '
                              + 'id="' + swfId + '" name="' + swfId + '" '
                              + 'swLiveConnect="true" '
                              + 'allowScriptAccess="sameDomain" '
                              + 'type="application/x-shockwave-flash" '
                              + 'pluginspage="' + protocol +'://www.macromedia.com/go/getflashplayer" '
                              + '></embed>'
                    
    var div = document.createElement("div");
    div.setAttribute("id", swfId + "Container");
    div.setAttribute("style", containerStyle);
    div.innerHTML = objectHTML;
    
    document.body.appendChild(div)
}

$(function() {
    Storage.init(inx.storage.private_init)
});


/*-- /mod/bundles/inx/src/inx/taskManager.js --*/


inx.taskManager = {

    taskList:{},
    active:true,
    task:function(id,name,time) { 
    
        time = time || 0;
        
        var key = id+":"+name;
        var val = inx.taskManager.taskList[key];
        time = val===undefined ? time : Math.max(time,val);
        inx.taskManager.taskList[key] = time;
        
        if(!inx.taskManager.timeout)
            inx.taskManager.timeout = setTimeout(function(){
                inx.taskManager.exec();
            });
    },
    
    deleteTasks:function(id) {
        var tasks = {};
        for(var key in inx.taskManager.taskList) {
            var d = key.split(":");
            if(id!=d[0])
                tasks[key] = inx.taskManager.taskList[key];
        }
        inx.taskManager.taskList = tasks;
    },
    
    tick:function() {
        for(var key in inx.taskManager.taskList)
            inx.taskManager.taskList[key] -= 50; 
        inx.taskManager.exec();
    },
    
    exec:function(dep) {
    
        if(!inx.taskManager.active)
            return;
            
        var taskNow = {};
        var taskDelayed = {};
        for(var key in inx.taskManager.taskList) {
            var time = inx.taskManager.taskList[key];
            if(time>0)
                taskDelayed[key] = time;
            else
                taskNow[key] = time;
        }
        inx.taskManager.taskList = taskDelayed;

        dep = dep ? dep+1 : 1;
        
        if(dep>100) {
            alert("Task depth limit. Stop at "+l[0][0]+" : "+l[0][1]);
            inx.taskManager.active = false;
            return;
        }
        
        inx.taskManager.timeout = false;
        
        var n = 0;
        for(var key in taskNow) {  
            var task = key.split(":");
            inx(task[0]).cmd(task[1]);
            n++;
        }
        
        if(n)
            inx.taskManager.exec(dep);
        
    }
}

setInterval(inx.taskManager.tick,50)


/*-- /mod/bundles/inx/src/inx/touch.js --*/


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


/*-- /mod/bundles/inx/src/inx/wheel.js --*/


// ------------------------------------------------
inx.wheel = function(e) {

    if(!e)
        e = window.event;

    var delta = e.wheelDelta;    
    // Для FF    
    if(e.detail)
        delta = -e.detail*40;
    
    var cmp = inx.cmp.fromElement(e.target);
    
    if(cmp.cmd("mousewheel",delta)===false) {
        if(e.preventDefault)
            e.preventDefault();
        e.returnValue = false;
        if(e.stopPropagation)
            e.stopPropagation();
    }    
}
window.onmousewheel = document.onmousewheel = inx.wheel;
if(window.addEventListener)
    window.addEventListener('DOMMouseScroll', inx.wheel, false);

