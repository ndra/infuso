<?

class board_task_log extends reflex {

    public static function all() { return reflex::get(get_class())->desc("created"); }
    public static function get($id) { return reflex::get(get_class(),$id); }

    public function task() { return $this->pdata("taskID"); }
    public function reflex_parent() { return $this->task(); }

    public function reflex_beforeCreate() {
        $this->data("created",util::now());
        $this->data("userID",user::active()->id());
    }

    public function reflex_afterStore() {
        $this->task()->updateTimeSpent();
    }

    public function reflex_afterDelete() {
        $this->task()->updateTimeSpent();
    }

    public function user() { return $this->pdata("userID"); }

    public function message() { return $this->data("text"); }
    public function msg() { return $this->message(); }
    public function text() { return $this->message(); }

    public function timeSpent() { return $this->data("timeSpent"); }

}
