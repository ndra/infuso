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

            $d = $task->timeSpent() + $task->timeSpentProgress() - $task->timeScheduled();
            $d = round($d/3600,1);

            if($d>0) {
                $text = "";
                $text.= "Просрочено ";
                $text.= $task->responsibleUser()->title().": ";
                $text.= "<a href='{$task->url()}' >";
                $text.= util::str($task->text())->ellipsis(100);
                $text.= "</a>";

                $text.= " &mdash; ".$d." ч.";

                $ret[] = array (
                    "text" => $text,
                );
            }

        }

        return $ret;
    }


}
