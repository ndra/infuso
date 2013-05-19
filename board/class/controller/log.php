<?

class board_controller_log extends mod_controller {

    public function postTest() {
        return user::active()->exists();
    }

    public function post_getLog($p) {

        $ret = array();

        $log = board_task_log::all();
        foreach($log as $item) {
            $ret[] = array(
                "userpick" => $item->user()->userpick()->preview(16,16),
                "user" => $item->user()->title(),
                "text" => $item->data("text"),
                "taskText" => util::str($item->task()->data("text"))->ellipsis(100),
                "taskID" => $item->task()->id(),
            );
        }

        return $ret;
    }

}
