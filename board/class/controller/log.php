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
            $log->eq("type",array(board_task_log::TYPE_COMMENT,board_task_log::TYPE_TASK_STATUS_CHANGED, board_task_log::TYPE_TASK_STATUS_RETURNED));
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

            $row = array(
                "type" => $item->data("type"),
                "userpick" => $item->user()->userpick()->preview(16,16)->crop(),
                "user" => $item->user()->nickname(),
                "text" => $item->data("text"),
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
