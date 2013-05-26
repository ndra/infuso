<?

class board_controller_log extends mod_controller {

    public function postTest() {
        return user::active()->exists();
    }

    public function post_getLog($p) {

        $ret = array();

        $log = board_task_log::all();

        if($taskID = $p["taskID"]) {
            $log->eq("taskID",$taskID);
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

}
