<?

class board_task extends reflex {

    /**
     * Возвращает список всех задач
     **/
	public static function all() {
	    return reflex::get(get_class())
	        ->asc("priority");
	}

    /**
     * Возвращает список видимых задач для активного пользователя
     **/
	public static function visible() {
	    $list = self::all();

        if(user::active()->checkAccess("board/viewAllTasks")) {
            return $list;
        }

	    $projects = board_project::visible()->limit(0)->idList();
	    $list->eq("projectID",$projects);
	    return $list;
	}

    /**
     * Возвращает задлачу по id
     **/
	public static function get($id) {
        return reflex::get(get_class(),$id);
    }

	public function project() {
        return $this->pdata("projectID");
    }

	public function reflex_parent() {
        return $this->project();
    }

	public static function reflex_root() {
	    return self::all()->title("Все задачи")->param("tab","system");
	}

	public function reflex_children() {
	    return array(
	        $this->getLogCustom()->title("Затраченное время"),
            $this->subtasks()->title("Подзадачи")
	    );
	}

	public function reflex_title() {
	    return util::str($this->data("text"))->ellipsis(50)."";
	}

	public function text() {
        return $this->data("text");
    }

	public function responsibleUser() {
        return $this->pdata("responsibleUser");
    }

	public function reflex_beforeCreate() {
	    $this->data("changed",util::now());
	    $this->data("created",util::now());
	}

	public function reflex_afterCreate() {
	    $this->log("Создано");
	}

	public function reflex_beforeStore() {

	    // Устанавливаем новую дату изменения только если задача активна
	    // Иначе мы можем влезть в статистику по прошлому периоду
	    if($this->field("status")->changed()) {
	        if($this->status()->active()) {
	            $this->data("changed",util::now());
            }
        }
        
        // Если это подзадача, ставим проект как у эпика
        if($this->data("epicParentTask")) {
            $this->data("projectID",$this->pdata("epicParentTask")->data("projectID"));
        }
        
	}

	public function updateTimeSpent() {
	    $this->data("timeSpent",$this->getLogCustom()->sum("timeSpent"));
	}

	public function getLogCustom() {
        return board_task_log::all()->eq("taskID",$this->id());
    }

	public function logCustom($text,$time=0) {
	    $this->getLogCustom()->create(array(
	        "taskID" => $this->id(),
	        "text" => $text,
	        "timeSpent" => $time,
	    ));
	}

    /**
     * Возвращает время, потраченное на задачу
     **/
	public function timeSpent() {
        return $this->data("timeSpent");
    }

    /**
     * Возвращает планируемое время
     **/
	public function timeScheduled() {
        return $this->data("timeScheduled");
    }

    /**
     * Возвращает статус задача (объект)
     **/
	public function status() {
	    return board_task_status::get($this->data("status"));
	}

    /**
     * Возвращает коллекцию подзадач
     **/
    public function subtasks() {
        return self::all()->eq("epicParentTask",$this->id());
    }

    /**
     * Возвращает число, показывающее сколько дней задача не меняла статус
     **/
	public function hangDays() {
	    return round((util::now()->stamp() - $this->pdata("changed")->stamp())/60/60/24);
	}

    /**
     * Возвращает процент выполненния задачи
     **/
    public function percentCompleted() {

        $a = $this->subtasks()->eq("status",array(2,3))->sum("timeScheduled");
        $b = $this->subtasks()->eq("status",array(0,1,2,3))->sum("timeScheduled");

        if(!$b) {
            return 0;
        }

        return $a / $b * 100;
    }

	public function stickerData($p=array()) {

	    $ret = array();

	    // Текст стикера
	    $ret["text"] = "<b>{$this->id()}.</b> ";

	    // Сколько задача висит в этом статусе
	    if($this->status()->active()) {
	        $d = (util::now()->stamp() - $this->pdata("changed")->stamp())/60/60/24;
	        $d = round($d);
	        if($d>=3) {
	            $data["text"].= "<span style='background:red;color:white;display:inline-block;padding:0px 4px;' >$d</span> ";
            }
	    }

	    // Бонусные задачи
	    if($this->data("bonus")) {
	        $ret["text"].= "<span style='color:white;background:green;font-style:italic;padding:0px 4px;'>б</span> ";
        }

	    // Просрочка
	    if($p["showHang"]) {
	        $h = $this->hangDays();
	        if($h>3) {
	            $ret["text"].= "<span style='color:white;background:red;padding:0px 4px;'>$h</span> ";
            }
	    }

	    if($p["showProject"])
	        $ret["text"].= "<b>".$this->project()->title().".</b> ";
	    $ret["text"].= util::str($this->data("text"))->ellipsis(200)->secure()."";

	    // Статусная часть стикера
	    $ret["info"] = "";
	    $ret["info"].= $this->timeSpent()."/".$this->timeScheduled()."ч. ";

	    // Цвет стикера
	    $ret["color"] = $this->data("color");

	    // Нижня подпись
	    if($this->responsibleUser()->exists())
	        $ret["bottom"] = "<nobr>".$this->responsibleUser()->title()."</nobr> ";

	    if($this->data("deadline"))
	        $ret["bottom"].= $this->pdata("deadlineDate")->left();

	    $ret["my"] = $this->responsibleUser()->id() == user::active()->id();

	    $ret["id"] = $this->id();

	    $ret["deadline"] = !!$this->data("deadline");
	    $ret["deadlineDate"] = $this->data("deadlineDate");

	    $d = util::now()->stamp() - $this->pdata("deadlineDate")->stamp();
	    $ret["fuckup"] = $ret["deadline"] && $d>0;

	    $ret["projectID"] = $this->project()->id();

        // Сортируются ли задачи в этом статусе
	    $ret["sort"] = $this->status()->stickerParam("sort");
	    if($p["sort"]===false || $p["sort"]===0) {
	        $ret["sort"] = false;
        }

        // Наличие прикрепленных файлов
        if($this->storage()->files()->count()) {
            $ret["attachment"] = true;
        }

        $ret["percentCompleted"] = $this->percentCompleted();

	    return $ret;
	}
	
}
