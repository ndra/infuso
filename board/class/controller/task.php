<?

class board_controller_task extends mod_controller {

    public function postTest() {
        return user::active()->exists();
    }

    /**
     * Экшн получения списка задач
     **/             
    public static function post_listTasks($p) {
    
        $limit = 100;
    
        $ret = array();

        // Статус для которого мы смотрим задачи
        $status = board_task_status::get($p["status"]);

        // Полный список задач
        $tasks = board_task::visible()->orderByExpr($status->order())->limit($limit);
        $tasks->eq("status",$p["status"]);

		// Учитываем поиск
        if($search = trim($p["search"])) {
        
            $search2 = util::str($search)->switchLayout();

            $tasks->joinByField("projectID");
            $tasks->like("text",$search)
                ->orr()->like("board_project.title",$search)
                ->orr()->like("text",$search2)
                ->orr()->like("board_project.title",$search2);
        }
        
        if(!$status->showEpicSubtasks()) {
            $tasks->eq("epicParentTask",0);
        }

        $tasks->page($p["page"]);

        // Задачи по цветам
        $stickerParams = $status->stickerParams();
        $stickerParams["showProject"] = true;

        $lastChange = null;

        $idList = array();
        foreach($p["idList"] as $item) {
            if(is_numeric($item)) {
                $idList[] = $item;
            }
        }

        $idList = implode(":",$idList);
        $idList2 = implode(":",$tasks->idList());

        if($idList == $idList2) {
            return false;
        }

        foreach($tasks as $task) {

            // Вывод дат
            if($status->showDates()) {
                $changeDate = $task->pdata("changed")->date()->txt();
                if($lastChange != $changeDate) {
                    $ret["data"][] = array(
                        "text" => $changeDate,
                        "dateMark" => $changeDate,
                    );
                    $lastChange = $changeDate;
                }
            }

            $ret["data"][] = $task->stickerData($stickerParams);
        }

        $ret["pages"] = $tasks->pages();
        $ret["sortable"] = $status->sortable();
        $ret["showCreateButton"] = $status->showCreateButton();

        return $ret;
    }

    /**
     * Возвращает список статусов для табов вверху страницы
     **/
    public static function post_taskStatusList($p) {
        $ret = array();
        foreach(board_task_status::all() as $status) {
            $n = board_task::visible()->eq("status",$status->id())->count();
            $ret[] = array(
                "id" => $status->id(),
                "title" => $status->title().($n ? " ($n)" : ""),
            );
        }
        return $ret;
    }

    /**
     * Возвращает список статусов для селекта
     **/
    public function post_enumStatuses() {
        $ret = array();
        foreach(board_task_status::all() as $status) {
            $ret[] = array(
                "id" => $status->id(),
                "text" => $status->title(),
            );
        }
        return $ret;
    }

    /**
     * Возвращает параметры одной задачи
     **/     
    public static function post_getTask($p) {

        $task = board_task::get($p["taskID"]);

        // Параметры задачи
        if(!user::active()->checkAccess("board/getTaskParams",array(
            "task" => $task
        ))) {
            mod::msg(user::active()->errorText(),1);
            return;
        }

        $statuses = array();
        foreach(board_task_status::all() as $status) {
            $statuses[] = array(
                "id" => $status->id(),
                "text" => $task->status()->id() == $status->id() ? "<u>".$status->title()."</u>" : $status->title(),
            );
        }

        return array(
            "title" => "Задача #".$task->id()." / ".$task->project()->title()." ({$task->status()->title()})",
            "text" => $task->data("text"),
            "color" => $task->data("color"),
            "timeScheduled" => $task->data("timeScheduled"),
            "projectID" => $task->data("projectID"),
            "projectTitle" => $task->project()->title(),
            "nextStatusID" => $task->status()->next()->id(),
            "nextStatusText" => $task->status()->next()->action(),
            "currentStatus" => $task->status()->id(),
            "statuses" => $statuses,
            "deadline" => $task->data("deadline"),
            "deadlineDate" => $task->data("deadlineDate"),
        );
    }

    /**
     * Контроллер сохранения задачи
     **/
    public static function post_saveTask($p) {

        $task = board_task::get($p["taskID"]);
        $data = util::a($p["data"])->filter("text","timeScheduled","projectID","color","deadline","deadlineDate")->asArray();

        // Параметры задачи
        if(!user::active()->checkAccess("board/updateTaskParams",array(
            "task" => $task
        ))) {
            mod::msg(user::active()->errorText(),1);
            return;
        }
        
        foreach($data as $key => $val) {
            $task->data($key,$val);
        }

        if ($task->fields()->changed()->count() > 0) {
            $task->logCustom("Изменение данных");
            mod::msg("Задача сохранена");
        }

        return true;
    }

    public function post_newTask() {

        if(!user::active()->checkAccess("board/newTask")) {
            mod::msg(user::active()->errorText(),1);
            return;
        }

        $task = reflex::create("board_task",array(
            "status" => board_task_status::STATUS_DRAFT,
		));
        return $task->id();
    }
    
    public function post_newDrawback() {

        // Параметры задачи
        if(!user::active()->checkAccess("board/newHindrance")) {
            mod::msg(user::active()->errorText(),1);
            return;
        }

        $task = reflex::create("board_task",array(
            "status" => board_task_status::STATUS_DRAFT,
            "hindrance" => true,
		));
        return $task->id();
    }

    /**
     * Возвращает задачи эпика
     **/
    public function post_getEpicSubtasks($p) {

        $task = board_task::get($p["taskID"]);

        // Параметры задачи
        if(!user::active()->checkAccess("board/getEpicSubtasks",array(
            "task" => $task
        ))) {
            mod::msg(user::active()->errorText(),1);
            return;
        }

        $ret = array();

        $tasks = $task->subtasks()->orderByExpr("`status` != 1")->asc("priority",true);
        $tasks->eq("status",array(board_task_status::STATUS_NEW,board_task_status::STATUS_IN_PROGRESS))->orr()->gt("changed",util::now()->shift(-60));
        foreach($tasks as $subtask) {

            $text = $subtask->data("text");

            if($subtask->status()->id()==board_task_status::STATUS_IN_PROGRESS) {
                $text.= " <nobr><img style='vertical-align:middle;' src='{$subtask->responsibleUser()->userpick()->preview(16,16)}' /> ";
                $text.= "{$subtask->responsibleUser()->title()}</nobr>";
            }

            $ret[] = array(
                "id" => $subtask->id(),
                "text" => $text,
                "timeScheduled" => round($subtask->timeSpent(),1)." / ".round($subtask->data("timeScheduled"),1),
                "completed" => $subtask->data("status") == 2 || $subtask->data("status") == board_task_status::STATUS_CANCELLED,
            );
        }
        return $ret;

    }

    public function post_addEpicSubtask($p) {

        $task = board_task::get($p["taskID"]);

        // Параметры задачи
        if(!user::active()->checkAccess("board/addEpicSubtask",array(
            "task" => $task
        ))) {
            mod::msg(user::active()->errorText(),1);
            return;
        }

        $task = reflex::create("board_task",array(
            "text" => $p["data"]["text"],
            "timeScheduled" => $p["data"]["timeScheduled"],
            "epicParentTask" => $task->id(),
        ));

    }

    /**
     * Меняет статус задачи
     **/
    public static function post_changeTaskStatus($p) {

        $task = board_task::get($p["taskID"]);

        // Параметры задачи
        if(!user::active()->checkAccess("board/changeTaskStatus",array(
            "task" => $task
        ))) {
            mod::msg(user::active()->errorText(),1);
            return;
        }

        $task->data("status",$p["status"]);

        // Если статус задачи "к исполнению", ответственным лицом становится текущий пользователь.
        if($task->status()->id()==1) {
            $task->data("responsibleUser",user::active()->id());
        }

        $time = $p["time"];

        // Текст про изменение статуса
        $statusText = $task->status()->action();

        // Ставим выполняющиеся задачи на паузу
        if($p["status"]==board_task_status::STATUS_IN_PROGRESS) {
            $xtasks = board_task::all()
                ->eq("responsibleUser",user::active()->id())
                ->eq("status",board_task_status::STATUS_IN_PROGRESS);
            foreach($xtasks as $xtask) {
                $xtask->pause();
            }
        }

        if($p["comment"]) {
            $statusText = $p["comment"]." ".$statusText;
        }

        $task->logCustom($statusText,$time);

        return true;
    }

    /**
     * Контроллер получения времени, потраченного на задачу
     **/
    public function post_getTaskTime($p) {

        $task = board_task::get($p["taskID"]);

        // Параметры задачи
        if(!user::active()->checkAccess("board/getTaskTime",array(
            "task" => $task
        ))) {
            mod::msg(user::active()->errorText(),1);
            return;
        }

        $date = $task->pdata("changed");
        $d = util::now()->stamp() - $date->stamp();
        $d -= $task->data("pauseTime");

        $hours = floor($d/60/60);
        $minutes = ceil($d/60)%60;

        return array(
            "hours" => $hours,
            "minutes" => $minutes,
        );

    }

    /**
     * Сохраняет сортировку набора задач
     **/
    public function post_saveSort($p) {

        // Параметры задачи
        if(!user::active()->checkAccess("board/sortTasks")) {
            mod::msg(user::active()->errorText(),1);
            return;
        }

        foreach($p["idList"] as $n=>$id) {
            $task = board_task::get($id);
            $task->data("priority",$n);
        }

        mod::msg("Сортировка сохранена");

    }

    public function post_pauseTask($p) {

        $task = board_task::get($p["taskID"]);

        // Параметры задачи
        if(!user::active()->checkAccess("board/pauseTask",array(
            "task" => $task,
        ))) {
            mod::msg(user::active()->errorText(),1);
            return;
        }

        $task->pauseToggle();

    }
    
    public function post_updateNotice($p) {

        $task = board_task::get($p["taskID"]);

        // Параметры задачи
        if(!user::active()->checkAccess("board/updateTaskNotice",array(
            "task" => $task,
        ))) {
            mod::msg(user::active()->errorText(),1);
            return;
        }

        $task->data("notice",$p["notice"]);

    }


/*



    public static function post_taskSendMessage($p) {

        if(!$text = trim($p["text"])) {
            mod::msg("Вы ничего не написали");
            return;
        }

        $task = board_task::get($p["taskID"]);
        if(!board_security::test("board:sendMessage",array("task"=>$task))) {
            mod::msg("Вы можете оставлять сообщения",1);
            return;
        }
        $task->getLogCustom()->create(array(
            "text" => $p["text"],
            "blah" => true,
        ));
    }  */


}
