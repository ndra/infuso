<?

class board_project extends reflex {

	/**
	 * Возвращает список всех проектов
	 **/
	public static function all() {
		return reflex::get(get_class())->desc("priority");
	}

	/**
	 * Возвращает список проектов, видимых для активного пользователя
	 **/	 	
	public static function visible() {

		if(user::active()->checkAccess("board:viewAllProjects")) {
			$projects = board_project::all();
        } else {

            $access = board_access::all()
                ->eq("userID",user::active()->id())
                ->neq("userID",0);

			$projects = board_project::all()->eq("id",$access->distinct("projectID"));
        }

		return $projects;
	}

    /**
     * Возвращает проект по id
     **/	     
	public static function get($id) {
		return reflex::get(get_class(),$id);
	}

	public function tasks() {
		return board_task::all()->eq("projectID",$this->id());
	}

	/**
	 * Возвращает количество потраченного на проект времени
	 **/	 
	public function timeSpent($status=null) {
		$tasks = board_task_log::all()->joinByField("taskID")->eq("board_task.projectID",$this->id());
		if($status!==null)
			$tasks->eq("board_task.status",$status);
		return $tasks->sum("timeSpent");
	}

	// Возвращает количество отведенного на проект времени
	public function timeScheduled($status=null) {
		$tasks = $this->tasks();
		if($status!==null)
			$tasks->eq("board_task.status",$status);
		return $tasks->sum("timeScheduled");
	}

	public function customer() {
		return $this->pdata("customerUserID");
	}

	public function info($status) {
		$info = "";
	    if($this->customer()->exists())
	    	$info.= "доступ:".$this->customer()->data("email")."<br/> ";

		switch($status) {

			case 10:
			    $count = $this->tasks()->eq(status,$status)->count();
			    if($count)
					$info.= "Ожидает запуска $count задач на ".$this->timeScheduled($status)." ч.";
				break;

			default:
				$info.= "потрачено ".$this->timeSpent($status)." / ".$this->timeScheduled($status)." ч.";
				break;
		}
		return $info;
	}

	public static function reflex_root() {
	    return self::all()->title("Проекты")->param("tab","system");
	}

	public function reflex_children() {
	    return array(
	        $this->tasks()->title("Задачи"),
	    );
	}
	
}
