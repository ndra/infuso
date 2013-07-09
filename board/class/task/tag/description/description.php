<?

class board_task_tag_description extends reflex {

    public function reflex_table() {

        return array(
            'name' => 'board_task_tag_description',
            'fields' => array (
                array (
                    'name' => 'id',
                    'type' => 'jft7-kef8-ccd6-kg85-iueh',
                ), array (
                    'name' => 'title',
                    'type' => 'textfield',
                    'editable' => 1,
                    'label' => 'Название',
                ),
            ),
        );
    }

    public static function all() {
        return reflex::get(get_class())->asc("title");
    }

    public static function get($id) {
        return reflex::get(get_class(),$id);
    }

}
