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
                "text" => $item->data("text"),
            );
        }

        return $ret;
    }

}
