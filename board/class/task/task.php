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

    public function reflex_url() {
        return "#".$this->id();
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
            $this->subtasks()->title("Подзадачи"),
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
            $this->data("paused",false);
            $this->data("pauseTime",0);
        }

        // Если это подзадача, ставим проект как у эпика
        if($this->data("epicParentTask")) {
            $this->data("projectID",$this->pdata("epicParentTask")->data("projectID"));
        }

        // Собираем список измененных полей
        $changed = array();
        foreach($this->fields() as $field) {
            if($field->changed()) {
                $changed[] = $field->name();
            }
        }

        mod::fire("board/taskChanged",array(
            "deliverToClient" => true,
            "taskID" => $this->id(),
            "sticker" => $this->stickerData(),
            "changed" => $changed,
		));
        
	}

    public function reflex_afterStore() {
        if($this->data("epicParentTask")) {

            $task = $this->pdata("epicParentTask");
            mod::fire("board/taskChanged",array(
                "deliverToClient" => true,
                "taskID" => $task->id(),
                "sticker" => $task->stickerData(),
                "changed" => array(),
        	));

        }
    }

	/**
	 * Временный метод для исправления структуры
	 **/
	public function reindex() {
        // Если это подзадача, ставим проект как у эпика
        if($this->data("epicParentTask")) {
            $this->data("projectID",$this->pdata("epicParentTask")->data("projectID"));
        }
	}

	/**
	 * Возвращает время, потраченное на задачу
	 * Оно складывается из времени, потраченного на саму задачу и времени на подзадачи
	 **/
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
     * Суммируются время, потраченное на задачу и на субзадачи
     **/
	public function timeSpent() {
        return $this->data("timeSpent") + $this->subtasks()->sum("timeSpent");
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

    public function isEpic() {
        return !$this->subtasks()->void();
    }

    /**
     * Возвращает процент выполненния задачи
     **/
    public function percentCompleted() {

        $a = $this->timeSpent();
        $b = $this->timeScheduled();

        if(!$b) {
            return 0;
        }

        $ret = $a / $b * 100;
        
        if($ret > 100) {
			$ret = 100;
        }
        
        return $ret;
    }
    
    /**
     * Ставит задачу на паузу
     **/
    public function pause() {

        if($this->data("paused")) {
            return;
        }

        $this->data("paused",util::now());

    }

    /**
     * снимает задачу с паузы
     **/
    public function resume() {

        if(!$this->data("paused")) {
            return;
        }

        $time = util::now()->stamp() - $this->pdata("paused")->stamp();
        $time+= $this->data("pauseTime");
        $this->data("pauseTime",$time);
        $this->data("paused",null);
    }

    /**
     * Ставит задачу на паузу / снимает с паузы
     **/
    public function pauseToggle() {
        if($this->data("paused")) {
            $this->resume();
        } else {
            $this->pause();
        }
    }

    public function uploadFilesCount() {
        $n = $this->storage()->files()->count();
        $this->data("files",$n);
    }

    /**
     * Возвращает данные для стикера
     **/
	public function stickerData() {

	    $ret = array(
            "backgroundImage" => null,
        );

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
        $h = $this->hangDays();
        if($h>3) {
            $ret["text"].= "<span style='color:white;background:red;padding:0px 4px;'>$h</span> ";
        }

	    $ret["text"].= "<b>".$this->project()->title().".</b> ";
	    $ret["text"].= util::str($this->data("text"))->ellipsis(200)->secure()."";

	    // Статусная часть стикера
	    $ret["info"] = "";
	    $ret["info"].= round($this->timeSpent(),1)."/".round($this->timeScheduled(),1)."ч. ";

	    // Цвет стикера
	    $ret["color"] = $this->data("color");

	    // Нижня подпись
	    if($this->responsibleUser()->exists()) {
	        $ret["bottom"] = "<nobr>".$this->responsibleUser()->title()."</nobr> ";
        }

	    if($this->data("deadline")) {
	        $ret["bottom"].= $this->pdata("deadlineDate")->left();
        }

	    $ret["my"] = $this->responsibleUser()->id() == user::active()->id();

	    $ret["id"] = $this->id();

        // Установленный дэдлайн
	    if($this->data("deadline")) {
            $ret["backgroundImage"] = "/board/res/task-time.png";
        }
	    $ret["deadlineDate"] = $this->data("deadlineDate");

        // Пропущенный дэдлайн
	    $d = util::now()->stamp() - $this->pdata("deadlineDate")->stamp();
	    if($this->data("deadline") && $d>0) {
            $ret["backgroundImage"] = "/board/res/task-time-fuckup.png";
        }
	    
	    if($this->data("hindrance")) {
			$ret["hindrance"] = true;
	    }

	    $ret["projectID"] = $this->project()->id();

        $ret["epic"] = $this->isEpic();

        // Наличие прикрепленных файлов
        if($this->data("files")) {
            $ret["attachment"] = true;
        }

        $ret["percentCompleted"] = $this->percentCompleted();

        if($this->data("paused")) {
            $ret["backgroundImage"] = "/board/res/img/icons64/pause.png";
        }

        $ret["tools"] = array();

        switch($this->status()->id()) {

            case board_task_status::STATUS_IN_PROGRESS:
                $ret["tools"][] = "pause";
                $ret["tools"][] = "done";
                break;

            case board_task_status::STATUS_NEW:
                $ret["tools"][] = "take";
                break;

            case board_task_status::STATUS_CHECKOUT:
                $ret["tools"][] = "complete";
                $ret["tools"][] = "revision";
                break;

        }

	    return $ret;
	}
	
}
