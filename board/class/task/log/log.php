<?

/**
 * Модель записи в логе
 **/
class board_task_log extends reflex {

    const TYPE_COMMENT = 1;
    const TYPE_TASK_MODIFIED = 3;
    const TYPE_TASK_STATUS_CHANGED = 4;

    public function reflex_table() {
        return array (
            'name' => 'board_task_log',
            'fields' => array (
                array (
                  'name' => 'id',
                  'type' => 'jft7-kef8-ccd6-kg85-iueh',
                ), array (
                  'id' => 'cvbqp5iqy5zxwesq41rd8as7w12tc9',
                  'name' => 'created',
                  'type' => 'x8g2-xkgh-jc52-tpe2-jcgb',
                  'editable' => '2',
                  'label' => 'Время записи',
                ), array (
                  'name' => 'userID',
                  'type' => 'pg03-cv07-y16t-kli7-fe6x',
                  'editable' => '2',
                  'label' => 'Пользователь',
                  'class' => 'user',
                ), array (
                  'name' => 'taskID',
                  'type' => 'pg03-cv07-y16t-kli7-fe6x',
                  'editable' => '2',
                  'label' => 'Задача',
                  'indexEnabled' => '1',
                  'class' => 'board_task',
                ), array (
                  'name' => 'text',
                  'type' => 'kbd4-xo34-tnb3-4nxl-cmhu',
                  'editable' => '1',
                  'label' => 'Текст',
                  'indexEnabled' => '0',
                ), array (
                  'name' => 'timeSpent',
                  'type' => 'yvbj-cgin-m90o-cez7-mv2j',
                  'editable' => '1',
                  'label' => 'Потрачено времени',
                ), array (
                  'name' => 'blah',
                  'type' => 'fsxp-lhdw-ghof-1rnk-5bqp',
                  'editable' => '1',
                  'label' => 'Треп',
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

    /**
     * Возвращает задачу к которой относится запись в логе
     **/
    public function task() {
        return $this->pdata("taskID");
    }

    public function reflex_parent() {
        return $this->task();
    }

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

    public function user() {
        return $this->pdata("userID");
    }

    public function message() {
        return $this->data("text");
    }

    public function msg() {
        return $this->message();
    }

    public function text() {
        return $this->message();
    }

    public function timeSpent() {
        return $this->data("timeSpent");
    }

}
