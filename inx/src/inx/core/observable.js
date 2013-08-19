// @link_with_parent

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
