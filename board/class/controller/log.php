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

        $log = board_task_log::all();

        if($taskID = $p["taskID"]) {
            $log->eq("taskID",$taskID);
        }

        // Только важные записи
        if($p["mode"]==0) {
            $log->eq("type",array(board_task_log::TYPE_COMMENT,board_task_log::TYPE_TASK_STATUS_CHANGED));
        }

        foreach($log as $item) {

            $row = array(
                "userpick" => $item->user()->userpick()->preview(16,16),
                "user" => $item->user()->title(),
                "text" => $item->data("text"),
                "time" => $item->pdata("created")->left(),
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
