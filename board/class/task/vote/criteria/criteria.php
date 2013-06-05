<?

class board_task_vote_criteria extends reflex {

    const SUBJECT_CREATOR = 1;
    const SUBJECT_EXECUTOR = 2;

    public function reflex_table() {

        return array(
            'name' => 'board_task_vote_criteria',
            'fields' => array (
                array (
                    'name' => 'id',
                    'type' => 'jft7-kef8-ccd6-kg85-iueh',
                ), array (
                    'name' => 'priority',
                    'type' => 'bigint',
                ), array (
                    'name' => 'title',
                    'type' => 'textfield',
                    "editable" => 1,
                ), array (
                    'name' => 'subject',
                    'type' => 'select',
                    'list' => array(
                        self::SUBJECT_CREATOR => "Создатель",
                        self::SUBJECT_EXECUTOR => "Исполнитель",
                    ), "editable" => 1,
                ),
            ),
        );
    }

    public static function all() {
        return reflex::get(get_class())->desc("priority")->param("sort",true);
    }

    public static function get($id) {
        return reflex::get(get_class(),$id);
    }

}
