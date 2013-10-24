<?

class board_controller_log extends mod_controller {

    public function postTest() {
        return user::active()->exists();
    }

    /**
     * Контроллер возвращает список щаписей в логе
     **/
    public function post_getLog($p) {

        $ret = array();

        $log = board_task_log::visible();

        if($taskID = $p["taskID"]) {
            $log->eq("taskID",$taskID);
        }

        // Только важные записи
        if($p["mode"]==0) {
            $log->eq("type",array(
                board_task_log::TYPE_COMMENT,
                board_task_log::TYPE_TASK_DONE,
                board_task_log::TYPE_TASK_COMPLETED,
                board_task_log::TYPE_TASK_REVISED,
                board_task_log::TYPE_TASK_CANCELLED,
            ));
        }

		$lastDate = null;
        foreach($log as $item) {
        
            $date = $item->pdata("created")->date()->txt();
            if($lastDate != $date) {
	            $ret[] = array(
	                "date" => $date,
				);
                $lastDate = $date;
            }
            
            $files = array();
            foreach($item->files() as $file) {
                $files[] = array(
                    "preview" => (string)$file->preview(32,32)->crop(),
                    "path" => (string)$file,
                );
            }

            switch($item->data("type")) {
                default:
                    $action = "";
                    break;

                case board_task_log::TYPE_TASK_TAKEN:
                    $action = "(Взято) ";
                    break;

                case board_task_log::TYPE_TASK_DONE:
                    $action = "(Выполнено) ";
                    break;

                case board_task_log::TYPE_TASK_COMPLETED:
                    $action = "(Завершено) ";
                    break;

                case board_task_log::TYPE_TASK_REVISED:
                    $action = "(Возвращено) ";
                    break;

                case board_task_log::TYPE_TASK_CANCELLED:
                    $action = "(Отменено) ";
                    break;

            }

            $row = array(
                "type" => $item->data("type"),
                "userpick" => $item->user()->userpick()->preview(16,16)->crop(),
                "user" => $item->user()->nickname(),
                "text" => $action.$item->data("text"),
                "time" => date("H:i",$item->pdata("created")->stamp()),
                "files" => $files,
            );

            if(!$taskID) {
                $row["taskText"] = util::str($item->task()->data("text"))->ellipsis(100);
                $row["taskID"] = $item->task()->id();
            }

            $ret[] = $row;

        }

        return $ret;
    }

    /**
     * Добавляет комментарий в задачу
     **/
    public static function post_sendMessage($p) {

        if(!$text = trim($p["text"])) {
            mod::msg("Вы ничего не написали");
            return;
        }

        $task = board_task::get($p["taskID"]);

        $task->getLogCustom()->create(array(
            "text" => $p["text"],
            "type" => board_task_log::TYPE_COMMENT,
        ));

        mod::msg("Сообщение отправлено");
    }

}
