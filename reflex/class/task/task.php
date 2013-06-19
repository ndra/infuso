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
					'name' => 'called',
					'type' => 'datetime',
					'label' => 'Вызвано',
					'editable' => '2',
				), array (
					'name' => 'nextLaunch',
					'type' => 'datetime',
					'label' => 'Следующий запуск',
					'editable' => '2',
				), array (
					'name' => 'completed',
					'type' => 'checkbox',
					'editable' => '2',
					'label' => 'Выполнено',
				), array (
					'name' => 'counter',
					'type' => 'bigint',
					'editable' => '2',
					'label' => 'Выполнено раз',
				), array (
					'name' => 'crontab',
					'type' => 'textfield',
					'editable' => '1',
					'label' => 'Шаблон (в формате crontab)',
					'help' => "Минуты, часы, день месяца, месяц, день недели",
				),
			),
		);
	}
	
    public function reflex_beforeCreate() {
        $this->data("created",util::now());
        $this->updateNextLaunchTime();
    }

    public function reflex_beforeStore() {
        if($this->field("crontab")->changed()) {
            $this->updateNextLaunchTime();
        }
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
            $params = util::a($params)->filter("class","query","method","params","crontab")->asArray();

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

    public static function addReflex($p) {
        self::add(array(
            "class" => get_class(),
            "method" => "execReflex",
            "params" => array(
                "class" => $p["class"],
                "method" => $p["method"],
                "params" => $p["params"],
            ),
        ));
    }

    public static function execReflex($p,$task) {
        $item = reflex::get($p["class"])
            ->asc("id")
            ->gt("id",$task->data("iterator"))
            ->one();

        if(!$item->exists()) {
            $task->data("completed",true);
            $task->store();
            return;
        }

        $task->data("iterator",$item->id());
        $task->store();

        $method = $p["method"];
        $params = $p["params"];
        $item->$method($params);

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
     * Обновляет время следующего запуска
     **/
    public function updateNextLaunchTime() {

        if(trim($this->data("crontab"))=="") {
            $this->data("nextLaunch",util::now());
        } else {
            $time = reflex_task_crontab::nextDate($this->data("crontab"));
            $this->data("nextLaunch",$time);
        }
    }

	/**
	 * Выполняет данную задачу
	 **/
    public function exec() {

        $this->updateNextLaunchTime();
    
        try {
        
			$this->data("called",util::now());
            $this->store();

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
	        
	        call_user_func($callback, $params, $this);

			$this->data("counter",$this->data("counter")+1);
	        $this->log("Выполняем");
	        
		} catch (Exception $ex) {
		
			$this->log("Exception: ".$ex->getMessage());
		    
		}
	        
    }

}
