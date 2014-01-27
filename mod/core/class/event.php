<?

namespace infuso\core;

/**
 * Класс, описывающий событие
 **/
class event extends component {

    private static $firedEvents = array();

    private $name = null;

    public function __construct($name,$params=array()) {
        $this->name = $name;
        $this->params($params);
    }

    /**
     * Возвращает имя события
     **/
    public function name() {
        return $this->name;
    }
    
    /**
     * Возвращает массив классов, которые могут реагировать на данное событие
     **/
    public function handlers() {
        $handlers = mod::service("classmap")->classmap("handlers");
        $handlers = $handlers[$this->name()];
        if(!$handlers) {
            $handlers = array();
        }
        return $handlers;
    }

    /**
     * Возвращает кэллбэков для данного события
     **/
    public function callbacks() {
        $callbacks = array();

        foreach($this->handlers() as $handler) {
            $callbacks[] = array(
                $handler,
                "on_".$this->name()
            );
        }
        return $callbacks;
    }

    /**
     * Вызывает данное событие и запускает обработчики
     **/
    public function fire() {
    
        $n = 0;
        while($this->firePartial($n)) {
            $n++;
        }
        
        if($this->deliverToClient()) {
        	self::$firedEvents[] = $this;
        }
    }

    /**
     * Метод разработан для вызова одного события в несколько подходов
     **/
    public function firePartial($from) {

        $callbacks = $this->callbacks();
        $callback = $callbacks[$from];
        
        if($callback) {
        
            profiler::beginOperation("event",$this->name(),$callback[0]."::".$callback[1]);
            call_user_func($callback,$this);
            profiler::endOperation();
            
            return true;
        }

        return false;
    }
    
    public function stop() {
        $this->param("*stopped",true);
    }
    
    public function stopped() {
        return !!$this->param("*stopped");
    }

    /**
     * Возвращает все события, вызванные в текущем запуске скрипта
     * @return array
     **/
    public static function all() {
        return self::$firedEvents;
    }
    
    public function dataWrappers() {
        return array(
            "deliverToClient" => "mixed",
		);
	}

}
