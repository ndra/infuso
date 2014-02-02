<?

namespace Infuso\Cms\Reflex;

use \Infuso\ActiveRecord\Record;
use \mod;

/**
 * Модель вкладки в каталоге
 **/
class rootTab extends Record {

	/**
	 * Описание таблицы
	 **/
	public function reflex_table() {

		return array (
			'name' => 'reflex_editor_rootTab',
			'fields' =>
			array (
				array (
				  'name' => 'id',
				  "type" => "id",
				),
				array (
				  'name' => 'title',
				  'type' => 'textfield',
				),
				array (
				  'name' => 'name',
				  'type' => 'textfield',
				),
				array (
				  'name' => 'priority',
				  'type' => 'bigint',
				),
				array (
				  'name' => 'icon',
				  'type' => 'file',
				),
			),
		);
	}
	
	public static function all() {
	    return Record::get(get_class())->desc("priority");
	}

	public static function allVisible() {

        $ret = array();
        foreach(self::all() as $tab) {
            if(sizeof($tab->roots())) {
                $ret[] = $tab;
            }
        }
        return $ret;

	}
	
	public static function get($id) {
	    return reflex::get(get_class(),$id);
	}

    public function roots() {
        $ret = array();
        foreach(mod::service("reflexEditor")->level0() as $root) {
            if($root->tab()==$this->name()) {
                $ret[] = $root;
            }
        }
        return $ret;
    }

    public function dataWrappers() {
        return array(
            "name" => "mixed/data",
        );
    }
	
	/**
	 * Создает новую вкладку
	 **/
	public static function create($p) {
	    return Record::create(get_class(),$p);
	}
	
	public function removeAll() {
	    return self::all()->delete();
	}
	
	public function icon() {
	    return $this->pdata("icon");
	}
	
}
