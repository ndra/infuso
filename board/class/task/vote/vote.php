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
                    'name' => 'ownerID',
                    'type' => 'link',
                    'class' => 'user',
                ), array (
                    'name' => 'subjectID',
                    'type' => 'link',
                    'class' => 'user',
                ), array (
                    'name' => 'criteriaID',
                    'type' => 'link',
                    'class' => 'board_task_vote_criteria',
                ), array (
                    'name' => 'score',
                    'type' => 'bigint',
                    'label' => 'Оценка (0-4)',
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

    public function criteria() {
        return $this->pdata("criteriaID");
    }

    public function reflex_parent() {
        return $this->task();
    }

    public function reflex_beforeCreate() {
        $this->data("created",util::now());
        $this->data("ownerID",user::active()->id());
    }

    public function reflex_beforeStore() {

        $subject = null;
        if($this->criteria()->data("subject")==board_task_vote_criteria::SUBJECT_CREATOR) {
            $subject = $this->task()->pdata("creator");
        } elseif($this->criteria()->data("subject")==board_task_vote_criteria::SUBJECT_EXECUTOR) {
            $subject = $this->task()->pdata("responsibleUser");
        }

        if($subject) {
            $this->data("subjectID",$subject->id());
        }
    }

    public function reflex_afterStore() {
        $this->task()->fireChangedEvent();
    }

    public function owner() {
        return $this->pdata("ownerID");
    }

}
