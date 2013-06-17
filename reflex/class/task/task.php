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
					'type' => 'v324-89xr-24nk-0z30-r243',
					'editable' => '1',
				), array (
					'name' => 'query',
					'type' => 'v324-89xr-24nk-0z30-r243',
					'editable' => '1',
					'indexEnabled' => '1',
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
					'name' => 'fromID',
					'type' => 'bigint',
					'editable' => '2',
					'label' => 'ID от',
				), array (
					'name' => 'time',
					'type' => 'datetime',
					'editable' => '2',
				), array (
					'name' => 'completed',
					'type' => 'checkbox',
					'editable' => '2',
					'label' => 'Выполнено',
				), array (
					'name' => 'priority',
					'type' => 'string',
					'editable' => '1',
					'label' => 'Приоритет',
				),
			),
		);
	}

    public function on_mod_cron() {
        self::execOne();
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
            $params = util::a($params)->filter("class","query","method","priority","params")->asArray();

        } else {

            $args = func_get_args();

            $params = array(
                "class" => $args[0],
                "query" => $args[1],
                "method" => $args[2],
                "priority" => $args[3],
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
        $params["params"] = serialize($params["params"]);

        $item = self::all()
            ->eq($params)
            ->one();

        if(!$item->exists()) {
            $item = reflex::create("reflex_task",$params);
        }

    }

    public function reflex_beforeCreate() {
        $time = round(util::now()->stamp()/60)*60;
        $this->data("time",util::date($time));
    }

    public function where() {

        $q = trim($this->data("query"));

        if(!$q)
            return 1;

        if(($q*1).""==$q."")
            return " `id`='$q' ";

        return $q;
    }

    public function items() {
        $items = reflex::get($this->data("class"));
        $items->where($this->where());
        $items->geq("id",$this->data("fromID"));
        $items->asc("id");
        return $items;
    }

    public function method() {
        return $this->data("method");
    }

    public function methodParams(){
        return unserialize($this->data("params"));
    }

    /**
     * Выполняет первое по приоритету задание в очереди
     **/
    public static function execOne() {

        $tasks = self::all()->eq("completed",0);
        $total = $tasks->count();

        if($total==0) {
            return;
        }

        // $n - хранится в сессии и увеличивается на 1 с каждым запуском крона
        $n = mod::session("01h1b4yw6kbz2l9y6orj");
        if(!$n) {
            $n = 0;
        }

        // Выбираем задачу в зависимости от $n
        // Т.о. каждый на запуск крона задачи будут поочередно вызываны
        $task = $tasks->limit(1)->page($n%$total+1)->one();

        mod::session("01h1b4yw6kbz2l9y6orj",$n+1);

        $task->exec();
    }

	/**
	 * Выполняет данную задачу
	 **/
    public function exec() {

        $method = $this->method();
        $params = $this->methodParams();
        if(!$params) {
            $params = array();
        }

        $fromID = -1;
        foreach($this->items()->limit(10) as $item) {

            $callback = array($item, $method);

            if(!is_callable($callback)) {
                $this->data("completed",true);
                $this->log("Невозможно выполнить, отменяем");
                return;
            }

            call_user_func_array($callback, $params);
            $fromID = $item->id();
        }

        if($fromID==-1) {
            $this->data("completed",true);
        } else {
            $this->data("fromID",$fromID+1);
        }

        $this->log("Выполняем");
    }

}
