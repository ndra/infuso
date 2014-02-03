<?

namespace Infuso\Board;

use \User;

class TaskStatus extends \Infuso\Core\Controller {

    const STATUS_DEMAND = 200;
    const STATUS_NEW = 0;
    const STATUS_BACKLOG = 0;
    const STATUS_IN_PROGRESS = 1;
    const STATUS_CHECKOUT = 2;
    const STATUS_COMPLETED = 3;
    const STATUS_DRAFT = 10;
    const STATUS_CANCELLED = 100;

    private static $all = array(

        self::STATUS_DEMAND => array(
    		"title" => "Заявки",
    		"action" => "Заявка",
    		"active" => true,
    		"stickerParams" => array(
    		    "sort" => true,
    		    "showHang" => true,
    		),
    	), self::STATUS_NEW => array(
    		"title" => "Бэклог",
    		"action" => "К исполнению",
    		"next" => 1,
    		"active" => true,
    		"stickerParams" => array(
    		    "sort" => true,
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
    		"title" => "Проверить",
    		"action" => "Я все сделал, проверьте",
    		"next" => 3,
    		"active" => true,
    		"order" => "changed desc",
    		"stickerParams" => array(
    		    "showHang" => true,
    		),
    	),
        self::STATUS_COMPLETED => array(
    		"title" => "Проверено",
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

        switch($this->id()) {

            case self::STATUS_IN_PROGRESS:
                $currentUserID = user::active()->id();
                return "`Infuso\\Board\\Task`.`responsibleUser` <> $currentUserID, `Infuso\\Board\\Task`.`responsibleUser`";

        }

    	$ret = self::$all[$this->status]["order"];
    	if(!$ret) {
    	    $ret = "`Infuso\\Board\\Task`.`priority` asc";
        }
    	return $ret;
    }

	/**
	 * Сортируются ли задачи в этом статусе
	 **/
    public function sortable() {
        if(in_array($this->id(),array(self::STATUS_NEW,self::STATUS_DEMAND))) {
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

    /**
     * Нужно ли выводить иконку создания задачи в списке задач
     **/
    public function showCreateButton() {
        if($this->id()==self::STATUS_NEW) {
            return true;
        }
        return false;
    }

    public function showDates() {
        if(in_array($this->id(),array(self::STATUS_COMPLETED,self::STATUS_CANCELLED,self::STATUS_CHECKOUT))) {
            return true;
        }
        return false;
    }

    public function visibleTasks() {
        $tasks = Task::visible();
        $tasks->eq("status",$this->id());
        if($this->id() != self::STATUS_IN_PROGRESS) {
            $tasks->eq("epicParentTask",0);
        }
        return $tasks;
    }

}
