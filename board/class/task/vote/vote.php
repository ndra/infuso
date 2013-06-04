<?

class board_task_vote extends reflex {

    public function reflex_table() {

        return array(
            'name' => 'board_task_vote',
            'fields' => array (
                array (
                    'name' => 'id',
                    'type' => 'jft7-kef8-ccd6-kg85-iueh',
                ), array (
                    'name' => 'created',
                    'type' => 'datetime',
                ), array (
                    'name' => 'taskID',
                    'type' => 'link',
                    'class' => 'board_task',
                ), array (
                    'name' => 'userID',
                    'type' => 'link',
                    'class' => 'user',
                ),
            ),
        );
    }

    public static function all() {
        return reflex::get(get_class())->desc("created");
    }

    public static function get($id) {
        return reflex::get(get_class(),$id);
    }

    public function task() {
        return $this->pdata("taskID");
    }

    public function reflex_parent() {
        return $this->task();
    }

    public function reflex_beforeCreate() {
        $this->data("created",util::now());
        $this->data("ownerID",user::active()->id());
    }

    public function owner() {
        return $this->pdata("ownerID");
    }

}
