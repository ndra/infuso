// @link_with_parent

/* 
При изменении размеров окна будет запланирована проверка размеров
*/

inx.box.manager = new function() {

    var m = this;
    
    var that = this;
    this.__buffer = {};    
   
    /**
     * Создает задачу проверить компонент с заданым id
     **/   
    this.watch = function(id) {
        this.__buffer[id] = true;
        if(!m.timeout) {
            m.timeout = setTimeout(m.processBuffer,0);
        }
    }
    
    /**
     * Выполняет задачу по проверке компонентов
     **/
    this.processBuffer = function() {
    
        m.timeout = null;
    
        var buffer = inx.deepCopy(m.__buffer);
        m.__buffer = {};
        
        for(var i in buffer) {
            m.checkItem(i);
        }

    }
    
    // Проверяет элемент на изменение размера один объект
    this.checkItem = function(id) {  
      
        var c = inx(id);
        
        // Хэш размера и содержимого
        // Изменение его заставит перерисовать внутренность компонента
        var hash = c.info("layoutHash");                
        var last = c.data("lastHash");
        
        // Хэш внешнего размера
        // Изменение его вызовет перерисовку родительского объекта
        var ohash = c.info("layoutOuterHash");
        var olast = c.data("lastOuterHash");
        
        var ret = false;
        
        if(hash!=last) {
            c.data("lastHash",hash);
            c.task("syncLayout");
            ret = true;
        }
        
        if(ohash!=olast) {            
            c.data("lastOuterHash",ohash);
            c.owner().task("syncLayout");
            ret = true;
        }

        
        return ret;
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