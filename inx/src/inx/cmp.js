// @link_with_parent

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
