<?

class board_task_vote_criteria extends reflex {

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
                        1 => "Создатель",
                        2 => "Исполнитель",
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
