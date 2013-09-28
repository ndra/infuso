// @link_with_parent

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