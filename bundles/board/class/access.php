<?

namespace Infuso\Board;

class Access extends \Infuso\ActiveRecord\Record {

    public function reflex_table() {

        return array(
            'name' => 'board_access',
            'fields' => array (
                array (
                    'name' => 'id',
                    'type' => 'jft7-kef8-ccd6-kg85-iueh',
                ), array (
                    'name' => 'userID',
                    'type' => 'link',
                    'class' => "user",
                ), array (
                    'name' => 'projectID',
                    'type' => 'link',
                    'class' => "board_project",
                ), array (
                    'name' => 'showComments',
                    'type' => 'checkbox',
                ), array (
                    'name' => 'showSpentTime',
                    'type' => 'checkbox',
                ), array (
                    'name' => 'editTasks',
                    'type' => 'checkbox',
                    "label" => "Редактирование задач"
                ), array (
                    'name' => 'editTags',
                    'type' => 'checkbox',
                    "label" => "Редактирование тэгов и заметок"
                ),
            ),
        );
    }

	/**
	 * Возвращает список всех объектов
	 **/
	public static function all() {
		return \Infuso\ActiveRecord\Record::get(get_class());
	}

    /**
     * Возвращает объект
     **/	     
	public static function get($id) {
		return \Infuso\ActiveRecord\Record::get(get_class(),$id);
	}

    public function user() {
        return $this->pdata("userID");
    }

    public function project() {
        return $this->pdata("projectID");
    }
	
}
