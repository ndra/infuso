<?

class board_task_tag extends reflex {

    public function reflex_table() {

        return array(
            'name' => 'board_task_tag',
            'fields' => array (
                array (
                    'name' => 'id',
                    'type' => 'jft7-kef8-ccd6-kg85-iueh',
                ), array (
                    'name' => 'taskID',
                    'type' => 'link',
                    'label' => 'Задача',
					'class' => "board_task",
                ), array (
                    'name' => 'tagID',
                    'type' => 'link',
                    'label' => 'Тэг',
					'class' => "board_task_tag_description",
                ),
            ),
        );
    }

    public static function all() {
        return reflex::get(get_class())->asc("id");
    }

    public static function get($id) {
        return reflex::get(get_class(),$id);
    }
    
    public function descr() {
        return $this->pdata("tagID");
    }
    
    public function reflex_title() {
        return $this->descr()->title();
    }

}
