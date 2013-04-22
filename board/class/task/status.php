<?

class board_task_status extends mod_controller {

    const STATUS_NEW = 0;
    const STATUS_IN_PROGRESS = 1;
    const STATUS_CHECKOUT = 2;
    const STATUS_COMPLETED = 3;
    const STATUS_DRAFT = 10;
    const STATUS_CANCELLED = 100;

    private static $all = array(
        -1 => array(
    		"title" => "-",
    		"stickerParams" => array(
    		    "sort" => false,
    		    "showVoidProjects" => false,
    		),
    	),
        self::STATUS_NEW => array(
    		"title" => "К исполнению",
    		"action" => "К исполнению",
    		"next" => 1,
    		"active" => true,
    		"stickerParams" => array(
    		    "sort" => true,
    		    "showVoidProjects" => true,
    		    "showHang" => true,
    		),
    	),
        self::STATUS_IN_PROGRESS => array(
    		"title" => "Выполняется",
    		"action" => "Буду делать",
    		"next" => 2,
    		"active" => true,
    		"stickerParams" => array(
    		    "showHang" => true,
    		),
    	),
        self::STATUS_CHECKOUT => array(
    		"title" => "На проверке",
    		"action" => "Я все сделал, проверьте",
    		"next" => 3,
    		"active" => true,
    		"order" => "changed desc",
    		"stickerParams" => array(
    		    "showHang" => true,
    		),
    	),
        self::STATUS_COMPLETED => array(
    		"title" => "Выполнено",
    		"action" => "Закрываю задачу",
    		"order" => "changed desc",
    	),
        self::STATUS_DRAFT => array(
    		"title" => "Черновик",
    		"action" => "Черновик",
    		"next" => 0,
    		"active" => true,
    		"stickerParams" => array(
    		    "sort" => true,
    		    "showVoidProjects" => true,
    		),
    	),
        self::STATUS_CANCELLED => array(
    		"title" => "Отменено",
    		"action" => "Отменяю",
    		"order" => "changed desc",
    	),
    );

    private $status;
    public function __construct($status=null) {
    	$this->status = $status;
    }

    public function get($status) {
    	return new self($status);
    }

    public static function all() {
    	$ret = array();
    	foreach(array_keys(self::$all) as $key)
    	    if($key>=0)
    			$ret[] = self::get($key);
    	return $ret;
    }

    /**
     * Возвращает название стстуса
     **/
    public function title() {
        return self::$all[$this->status]["title"];
    }

    /**
     * Возвращает текст следующего действия
     **/
    public function action() {
        return self::$all[$this->status]["action"];
    }

    /**
     * Возвращает объект следующего статуса
     **/
    public function next() {
        return self::get(self::$all[$this->status]["next"]);
    }

    /**
     * Возвращает сортировку задач в этом статусе
     **/
    public function order() {
    	$ret = self::$all[$this->status]["order"];
    	if(!$ret)
    	    $ret = "priority asc";
    	return $ret;
    }

    public function sortable() {
        if(in_array($this->id(),array(self::STATUS_NEW))) {
            return true;
        }
        return false;
    }

    /**
     * Возвращает id статуса
     **/
    public function id() {
        return $this->status;
    }

    /**
     * Возвращает активный статус
     **/
    public function active() {
        return  self::$all[$this->status]["active"];
    }

    /**
     * Возвращает параметры стикера с задачей
     **/
    public function stickerParams() {
        $ret = self::$all[$this->status]["stickerParams"];
        if(!$ret)
            $ret = array();
    	return $ret;
    }

    /**
     * Возвращает параметры стикера с задачей
     **/
    public function stickerParam($key) {
        $ret = $this->stickerParams();
        return $ret[$key];
    }

}
