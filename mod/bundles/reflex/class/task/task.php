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
					'name' => 'lastErrorDate',
					'type' => 'datetime',
					'editable' => '2',
					'label' => 'Когда была последняя ошибка',
				), array (
					'name' => 'crontab',
					'type' => 'textfield',
					'editable' => '1',
					'label' => 'Шаблон (в формате crontab)',
					'help' => "Минуты, часы, день месяца, месяц, день недели",
				), array (
					'name' => 'origin',
					'type' => 'textfield',
					'editable' => '1',
					'label' => 'Источник',
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
        return reflex::get(get_class())->desc("nextLaunch",true);
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
    
        // Разруливаем олдскульный случай, когда параметры передавались не массивом а в аргументах
    
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
        
        // Разруливаем reflex-задачи
        
        $mode = "reflex";

        try {
	        $rmethod = new ReflectionMethod($params["class"],$params["method"]);
	        if($rmethod->isStatic()) {
	            $mode = "static";
	        }
		} catch (Exception $ex) {}

        if($mode == "reflex") {
            reflex_task_reflex::add($params);
            return;
        }
        
		// Если мы дошли до этого места, у нас обычная статическая задача
        
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
            
		$params["origin"] = reflex_task_handler::$origin;

        if(!$item->exists()) {
            $item = reflex::create("reflex_task",$params);
        } else {
            if($params["origin"]) {
            	$item->data("origin",$params["origin"]);
            	$item->store();
            }
        }

    }

	/**
     * Возвращает вызываемый метод
     **/
    public function method() {
        return $this->data("method");
    }
    
    /**
     * Возвращает вызываемый класс
     **/
    public function className() {
        return $this->data("class");
    }

	/**
     * Возвращает параметры вызываемого метода
     **/
    public function methodParams(){
        $params = $this->pdata("params");
        if(!is_array($params)) {
            $params = array();
        }
        return $params;
    }

    /**
     * Обновляет время следующего запуска
     **/
    public function updateNextLaunchTime() {

		// Таймстэмп
		if(preg_match("/^\d+$/",$this->data("crontab"))) {
            $this->data("nextLaunch",$this->data("crontab"));
            
        // Mysql Date format
        } elseif(preg_match("/\d{4}-\d{2}-\d{2}\s(\d{2}\:\d{2}\:\d{2})?/",$this->data("crontab"))) {
            $this->data("nextLaunch",$this->data("crontab"));
            
        // Пустая строка
        } elseif(trim($this->data("crontab"))=="") {
            $this->data("nextLaunch",util::now());
            
		// Прочее - кронтаб
        } else {
            $time = reflex_task_crontab::nextDate($this->data("crontab"));
            $this->data("nextLaunch",$time);
        }
    }

    /**
     * Одноразовая ли задача
     **/
    public function oneTime() {
    
        // Если время выполнения задано как таймстэмп - задача одноразовая
        if(preg_match("/^\d+$/",$this->data("crontab"))) {
            return true;
        }

		// MySQL-формат - одноразовая задача
        if(preg_match("/\d{4}-\d{2}-\d{2}\s(\d{2}\:\d{2}\:\d{2})?/",$this->data("crontab"))) {
            return true;
        }

        return false;

    }

	/**
	 * Выполняет данную задачу
	 **/
    public function exec() {

        $this->updateNextLaunchTime();
    
        try {
        
			$this->data("called",util::now());

            if($this->oneTime()) {
                $this->data("completed",true);
            }

            $this->store();

	        $method = $this->method();
	        $class = $this->className();
	        $params = $this->methodParams();
	        
	        $callback = array($class, $method);

	        if(!is_callable($callback)) {
	            throw new Exception("{$callback[0]}::{$callback[1]} is not a callback");
	            return;
	        }
	        
	        call_user_func($callback, $params, $this);

			$this->data("counter",$this->data("counter")+1);
	        $this->log("Выполняем");
	        
		} catch (Exception $ex) {
		
		    $this->data("lastErrorDate",util::now());
			$this->log("Exception: ".$ex->getMessage());
		    
		}
	        
    }

}
