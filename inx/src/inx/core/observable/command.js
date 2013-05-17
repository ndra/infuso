// @link_with_parent
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
