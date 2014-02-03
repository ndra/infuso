<?

namespace Infuso\Board;

use  \Infuso\ActiveRecord\Record;
use  \User;

/**
 * Модель записи в логе
 **/
class TaskLog extends Record {

    const TYPE_COMMENT = 1;
    const TYPE_TASK_MODIFIED = 3;
    const TYPE_TASK_STOPPED = 6;
    const TYPE_TASK_TAKEN = 7;
    const TYPE_TASK_DONE = 8;
    const TYPE_TASK_COMPLETED = 9;
    const TYPE_TASK_REVISED = 10;
    const TYPE_TASK_CANCELLED = 11;
    const TYPE_TASK_MOVED_TO_BACKLOG = 12;

    public function reflex_table() {
        return array (
            'name' => 'board_task_log',
            'fields' => array (
                array (
                  'name' => 'id',
                  'type' => 'jft7-kef8-ccd6-kg85-iueh',
                ), array (
                  'name' => 'created',
                  'type' => 'x8g2-xkgh-jc52-tpe2-jcgb',
                  'editable' => '2',
                  'label' => 'Время записи',
                ), array (
                  'name' => 'userID',
                  'type' => 'pg03-cv07-y16t-kli7-fe6x',
                  'editable' => '2',
                  'label' => 'Пользователь',
                  'class' => User::inspector()->className(),
                ), array (
                  'name' => 'taskID',
                  'type' => 'pg03-cv07-y16t-kli7-fe6x',
                  'editable' => '2',
                  'label' => 'Задача',
                  'indexEnabled' => '1',
                  'class' => Task::inspector()->className(),
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
                ), array (
                    'name' => 'type',
                    'type' => 'select',
                    'editable' => '1',
                    'label' => 'Тип',
                    'list' => array(
                        self::TYPE_COMMENT => "Комментарий",
                    ),
                ), array (
                    'name' => 'files',
                    'type' => 'string',
                    'editable' => '2',
                    'label' => 'Папка с файлами',
                ),
            ),
        );
    }

    /**
     * Возвращает коллекцию всех записей в логе
     **/
    public static function all() {
        return Record::get(get_class())->desc("created");
    }

    /**
     * Возвращает коллекцию записей в логе, видимых для активного пользователя
     **/
    public static function visible() {
        $visibleTasks = Task::visible();
        $log = self::all()->joinByField("taskID",$visibleTasks);
        return $log;
    }

    /**
     * Возвращает запись в логе по id
     **/
    public static function get($id) {
        return Record::get(get_class(),$id);
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

    public function reflex_afterCreate() {

        if($this->data("type") == self::TYPE_COMMENT) {

            $task = $this->task();
            $users = array($task->responsibleUser()->id(),$task->pdata("creator")->id());
            $users = array_unique($users);

            // Рассылаем комментарий ответственному лицу и автору
            foreach($users as $userID) {
                if($userID != $this->user()->id()) {
                    $user = user::get($userID);
                    $taskText = util::str($this->task()->text())->ellipsis(100);
                    $taskURL = $this->task()->url();
                    $comment = $this->text();
                    $user->mailer()
                        ->subject("Пользователь {$this->user()->title()} прокомментировал задачу <a href='{$taskURL}' >«{$taskText}»</a>: {$comment}")
            			->send();
                }
            }
        }
    }

    public function reflex_afterStore() {
        $this->task()->updateTimeSpent();
    }

    public function reflex_afterDelete() {
        $this->task()->updateTimeSpent();
    }

    /**
     * Возвращает пользователя от которого сделана запись
     **/
    public function user() {
        return $this->pdata("userID");
    }

    /**
     * Возвращает текст записи
     **/
    public function message() {
        return $this->data("text");
    }

    /**
     * Возвращает текст записи
     **/
    public function msg() {
        return $this->message();
    }

    /**
     * Возвращает текст записи
     **/
    public function text() {
        return $this->message();
    }

    /**
     * Возвращает потраченное время
     **/
    public function timeSpent() {
        return $this->data("timeSpent");
    }

    /**
     * Возвращает список файлов, прикрепелнных к записи лога
     * (не путать с файлами задачи)
     **/
    public function files() {

        if($this->data("files")) {
            return $this->task()->storage()->setPath("/log/".$this->data("files"))->files();
        } else {
            return array();
        }

    }

}
