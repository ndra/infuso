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
            $tasks->joinByField("projectID");
            $tasks->like("text",$search)->orr()->like("board_project.title",$search);
        }
        
        if(!$status->showEpicSubtasks()) {
            $tasks->eq("epicParentTask",0);
        }

        $tasks->page($p["page"]);

        // Задачи по цветам
        $stickerParams = $status->stickerParams();
        $stickerParams["showProject"] = true;
        foreach($tasks as $task) {
            $ret["data"][] = $task->stickerData($stickerParams);
        }

        $ret["pages"] = $tasks->pages();
        $ret["sortable"] = $status->sortable();

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
            "title" => "Задача #".$task->id()." (".$task->project()->title().")",
            "text" => $task->data("text"),
            "nextStatusID" => $task->status()->next()->id(),
            "nextStatusText" => $task->status()->next()->action(),
            "statuses" => $statuses
        );
    }

    /**
     * Контроллер сохранения задачи
     **/
    public static function post_saveTask($p) {

        $task = board_task::get($p["taskID"]);
        $data = util::a($p["data"])->filter("text")->asArray();

        // Параметры задачи
        if(!user::active()->checkAccess("board/updateTaskParams",array(
            "task" => $task
        ))) {
            mod::msg(user::active()->errorText(),1);
            return;
        }

        foreach($data as $key=>$val) {
            $task->data($key,$val);
        }

        $task->logCustom("Изменение данных");

        return true;
    }

    public function post_newTask() {

        // Параметры задачи
        if(!user::active()->checkAccess("board/newTask",array(
            "task" => $task
        ))) {
            mod::msg(user::active()->errorText(),1);
            return;
        }

        $task = reflex::create("board_task",array(
            "status" => board_task_status::STATUS_DRAFT,
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

        $statusList = array(
            board_task_status::STATUS_NEW,
            board_task_status::STATUS_IN_PROGRESS,
            board_task_status::STATUS_COMPLETED,
            board_task_status::STATUS_CHECKOUT
        );

        foreach($task->subtasks()->eq("status",$statusList) as $subtask) {

            $text = $subtask->data("text");

            if($subtask->status()->id()==board_task_status::STATUS_IN_PROGRESS) {
                $text.= " <nobr><img style='vertical-align:middle;' src='{$subtask->responsibleUser()->userpick()->preview()}' /> ";
                $text.= "{$subtask->responsibleUser()->title()}</nobr>";
            }

            $ret[] = array(
                "id" => $subtask->id(),
                "text" => $subtask->data("priority").". ".$text,
                "timeScheduled" => $subtask->data("timeScheduled"),
                "completed" => $subtask->data("status") == 2,
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

        $task->logCustom($statusText,$time);

        return true;
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

    public function post_uploadFile($p) {
        mod::msg($_FILES);
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
