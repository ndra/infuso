<?

class board_controller_messages extends mod_controller {

    public function postTest() {
        return user::active()->exists();
    }

    /**
     * Экшн получения списка задач
     **/             
    public static function post_getMessages($p) {

        $ret = array();

        $tasks = board_task::all()
            ->eq("status",board_task_status::STATUS_IN_PROGRESS)
            ->limit(0);

        foreach($tasks as $task) {

            $d = $task->timeSpent() + $task->timeSpentProgress() - $task->timeScheduled()*3600;
            $d = round($d/3600,1);

            if($d>0) {
                $text = "";
                $text.= "Просрочено ";
                $text.= $task->responsibleUser()->title().": ";
                $text.= "<a href='{$task->url()}' >";
                $text.= util::str($task->text())->ellipsis(100);
                $text.= "</a>";
                $text.= " &mdash; ".$d." ч.";

                $hash = md5("overcooking/{$task->id()}/{$task->data(changed)}");

                $ret[] = array (
                    "text" => $text,
                    "hash" => $hash,
                );
            }

        }

        $ret2 = array();
        foreach($ret as $item) {
            if(!self::messageHidden($item["hash"])) {
                $ret2[] = $item;
            }
        }

        return $ret2;
    }

    public function messageHidden($hash) {
        return mod_session::session()->hiddenMessages->valueExists($hash);
    }

    public static function post_hideMessage($p) {
        $hash = $p["hash"];
        mod_session::session()->hiddenMessages->push($p["hash"]);
    }


}
