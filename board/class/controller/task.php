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

        // Задачи по цветам
        $stickerParams = $status->stickerParams();
        $stickerParams["showProject"] = true;
        foreach($tasks as $task) {
            $ret["data"][] = $task->stickerData($stickerParams);
        }

        return $ret;
    }

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
     * Возвращает параметры одной задачи
     **/     
    public static function post_getTask($p) {

        $task = board_task::get($p["taskID"]);

        $log = "";
        foreach($task->getLogCustom() as $item) {
            $log.= $item->editor()->render();
        }

        $statuses = array();
        foreach(board_task_status::all() as $status) {
            if($status->id()!=$task->status()->id())
                $statuses[] = array(
                    "id" => $status->id(),
                    "title" => $status->action(),
                );
        }

        return array(
            "title" => "Задача #".$task->id()." (".$task->project()->title().")",
            "text" => $task->data("text"),
            "priority" => $task->data("priority"),
            "color" => $task->data("color"),
            "bonus" => $task->data("bonus"),
            "timeSceduled" => $task->data("timeSceduled"),
            "project" => $task->data("projectID"),
            "status" => $task->data("status"),
            "deadline" => $task->data("deadline"),
            "deadlineDate" => $task->data("deadlineDate"),
            "statuses" => $statuses,
            "nextStatus" => $task->status()->next(),
            "log" => $log,
        );
    }

    public static function post_saveTask($p) {

        $data = array();

        // Описание задачи
        if(user::active()->checkAccess("board:updateTaskText",array("task"=>$task))) {

            if($text = trim($p["data"]["text"])) {
                $data["text"] = $text;
            } else {
                mod::msg("Текст обязателен для заполнения",1);
                return;
            }
        }

        // Параметры задачи
        if(user::active()->checkAccess("board:updateTaskParams",array("task"=>$task))) {

            // Цвет
            $data["color"] = $p["data"]["color"];

            // Планируемое время
            $data["timeSceduled"] = $p["data"]["timeSceduled"];

            // Затраченное время
            $data["bonus"] = $p["data"]["bonus"];

            // Дэдлайн
            $data["deadline"] = $p["data"]["deadline"];
            $data["deadlineDate"] = $p["data"]["deadlineDate"];

            // Проект
            $project = board_project::get($p["data"]["project"]);
            if(!$project->exists()) {
                mod::msg("Указанный проект не существует",1);
                return;
            }
            $data["projectID"] = $project->id();
        }

        if(!sizeof($data)) {
            mod::msg("Вы не можете редактировать эту задачу",1);
            return;
        }

        if($p["taskID"]=="new")
            $task = reflex::create("board_task",array("status"=>$p["status"]));
        else
            $task = board_task::get($p["taskID"]);

        foreach($data as $key=>$val)
            $task->data($key,$val);

        $task->logCustom("Изменение данных");

        return true;
    }

    public static function post_changeTaskPriority($p) {

        if(!board_security::test("board:changeTaskPriority")) {
            mod::msg("Вы не можете изменять приоритет заданий",1);
            return;
        }

        // Выстраиваем задачи по приоритету
        $n = 0;
        foreach($p["idList"] as $taskID) {
            if($taskID!=$p["taskID"]) {
                board_task::get($taskID)->data("priority",$n*2);
                $n++;
            }
        }
        board_task::get($p["taskID"])->data("priority",$p["position"]*2-1);
    }

    public static function post_changeTaskStatus($p) {

        $task = board_task::get($p["taskID"]);

        if(!board_security::test("board:changeTaskStatus",array("task"=>$task,"status"=>$p["status"]))) {
            mod::msg("Вы не можете изменять статус этого задания",1);
            return;
        }

        $task->data("status",$p["status"]);

        // Если статус задачи "к исполнению", ответственным лицом становится текущий пользователь.
        if($task->status()->id()==1)
            $task->data("responsibleUser",user::active()->id());

        $time = $p["time"];

        // Текст про изменение статуса
        $statusText = $task->status()->action();

        $task->logCustom($statusText,$time);
        return true;
    }

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
    }


}
