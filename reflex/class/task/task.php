<?

/**
 * Реализация очереди задач в reflex
 **/
class reflex_task extends reflex implements mod_handler {

	public static function reflex_table() {
	
		return array (
			'name' => 'reflex_task',
			'fields' => array (
				array (
					'name' => 'id',
					'type' => 'id',
				), array (
					'name' => 'class',
					'type' => 'textfield',
					'editable' => '1',
				), array (
					'name' => 'method',
					'type' => 'textfield',
					'editable' => '1',
				), array (
					'name' => 'params',
					'type' => 'array',
					'editable' => '1',
					'label' => 'Доп. параметры',
				), array (
					'name' => 'iterator',
					'type' => 'bigint',
					'editable' => '2',
					'label' => 'Итератор',
				), array (
					'name' => 'created',
					'type' => 'datetime',
					'editable' => '2',
				), array (
					'name' => 'executed',
					'type' => 'datetime',
					'editable' => '2',
				), array (
					'name' => 'completed',
					'type' => 'checkbox',
					'editable' => '2',
					'label' => 'Выполнено',
				), array (
					'name' => 'counter',
					'type' => 'bigint',
					'editable' => '1',
					'label' => 'Выполнено раз',
				), array (
					'name' => 'pattern',
					'type' => 'textfield',
					'editable' => '1',
					'label' => 'Шаблон (в формате crontab)',
					'help' => "Минуты, часы, день месяца, месяц, день недели",
				),
			),
		);
	}
	
    public function reflex_beforeCreate() {
        //$time = round(util::now()->stamp()/60)*60;
        $this->data("created",util::now($time));
    }

    /**
     * Возвращает коллекцию задач
     **/
    public static function all() {
        return reflex::get(get_class())->desc("priority")->desc("time",true);
    }

    /**
     * Возвращает задачу по id
     **/
    public static function get($id) {
        return reflex::get(get_class(),$id);
    }
    
    /**
     * Возвращает последнее разрешенное время выполнения
     * К примеру, если pattern = "0 10 * * *" - выполнять в 10-00 каждый день
     * и сейчас 11-55, то метод вернет объект даты, соответствующий сегодня 10-00
     **/
    public function lastAvailableExecutionTime() {
    
        $pettern = array("*","*","*/7","*/3","*/5");
        $pettern = array("*","*","*","*","*");
        
        $now = util::now();
        $ret = array();
    
		$secPattern = $pattern[0];
		switch($secPattern) {
		    case "*";
		    $ret["sec"] = $now->seconds();
		}
		
		var_export($ret);
    
    }

    /**
     * Добавляет новое задание очередь. Задание - это выполнение заданного метода заданной модели по крону.
     * Перебирается полная коллекция элементова можели или ее часть, если задано условие "query"
     * Если задание уже есть, повторного добавления не будет
     *
     * reflex_task::create(
     *    "class" => ..,
     *    "method" => ..,
     *    "query" => ..,
     *    "priority" => ..,
     *    "params" => ..,
     * )
     **/
    public static function add($params) {

        if(is_array($params)) {
            $params = util::a($params)->filter("class","query","method","params")->asArray();

        } else {

            $args = func_get_args();

            $params = array(
                "class" => $args[0],
                "method" => $args[2],
                "params" => $args[3] ? $args[3] : array(),
            );
        }
        
        if(!$params["class"]) {
            throw new Exception("Параметр <b>class</b> не указан");
        }
        
        if(!$params["method"]) {
            throw new Exception("Параметр <b>method</b> не указан");
        }

        $params["completed"] = 0;
        $params["params"] = mod::field("array")->value($params["params"])->value();

        $item = self::all()
            ->eq($params)
            ->one();

        if(!$item->exists()) {
            $item = reflex::create("reflex_task",$params);
        }

    }

    public function method() {
        return $this->data("method");
    }
    
    public function className() {
        return $this->data("class");
    }

    public function methodParams(){
        return unserialize($this->data("params"));
    }

	/**
	 * Выполняет данную задачу
	 **/
    public function exec() {
    
        try {

	        $method = $this->method();
	        $class = $this->className();
	        $params = $this->methodParams();
	        if(!$params) {
	            $params = array();
	        }
	        
	        $callback = array($class, $method);

	        if(!is_callable($callback)) {
	            throw new Exception("{$callback[0]}::{$callback[1]} is not a callback");
	            return;
	        }
	        
	        call_user_func_array($callback, $params);

			$this->data("executed",util::now());
			$this->data("counter",$this->data("counter")+1);
	        $this->log("Выполняем");
	        
		} catch (Exception $ex) {
		
			$this->log("Exception: ".$ex->getMessage());
		    
		}
	        
    }

}
