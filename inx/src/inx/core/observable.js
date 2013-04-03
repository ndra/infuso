// @link_with_parent

inx.observable = Base.extend({

    constructor:function(p) {
        // Подписываеся на события
        this.listeners = {};        
        for(var i in p.listeners)
            this.on(i,p.listeners[i]);

        // Записываем параметры в свойства объекта
        // Если у объекта уже есть свойство, оно не перезаписывается
        for(var i in p)
            if(this[i]===undefined)
                this[i] = p[i];
    },
    
    id:function() { return this.private_id; },
    
    cmd:function(name) {
    
        // Делаем чтобы рндер выполнялся только 1 раз
        if(name=="render") {
            if(this.private_z74gi3f1in)
                return;            
            this.private_z74gi3f1in = true;
        }
    
        if(inx.debug) {
            inx.observable.debug.cmd++;
            inx.observable.debug.cmdCountByID[this.id()] = (inx.observable.debug.cmdCountByID[this.id()] || 0) + 1  ;
            
            if(!inx.observable.debug.cmds[this.id()])
                inx.observable.debug.cmds[this.id()] = {};
            if(!inx.observable.debug.cmds[this.id()][name])
                inx.observable.debug.cmds[this.id()][name] = 0;
            inx.observable.debug.cmds[this.id()][name]++;
        }
        
        var a = [];
        for(var i=1;i<arguments.length;i++)
            a[i-1] = arguments[i];
            
        name = "cmd_"+name;
            
        var ret = null;
        
        if(inx.debug)
            inx.observable.debug.stack.push(this.type+":"+name);
        
        if(typeof(this[name])=="function")
            try {
                var t1 = new Date().getTime();
                ret = this[name].apply(this,a);
                var t2 = new Date().getTime();
                var time = t2-t1;
                inx.observable.debug.totalTime[name] = (inx.observable.debug.totalTime[name] || 0) + time;
            } catch(ex) {
                ex.method = name;
                this.private_showError(ex);
            }
            
        if(inx.debug)
            inx.observable.debug.stack.pop();
            
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
        var a = []; for(var i=1;i<arguments.length;i++)a[i-1]=arguments[i];
        name = "info_"+name;
        if(typeof(this[name])=="function")
            try {
                var t1 = new Date().getTime();
                var ret = this[name].apply(this,a);
                var t2 = new Date().getTime();
                var time = t2-t1;
                inx.observable.debug.totalTime[name] = (inx.observable.debug.totalTime[name] || 0) + time;
                return ret;
            } catch(ex) {
                ex.method = name;
                this.private_showError(ex);
            }
               
    },    

    on:function(event,a,b) { inx(this.id()).on(event,a,b); },
    
    call:function(p,s,f,m) { return inx(this.id()).call(p,s,f,m); },
    
    suspendEvents:function() {this.__eventsDisabled=true},
    unsuspendEvents:function() {this.__eventsDisabled=false},

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

inx.observable.debug = {
    stack:[],
    cmd:0,
    cmds:{},
    cmdCountByID:{},
    totalTime:{},
}
