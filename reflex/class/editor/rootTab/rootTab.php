<?

/**
 * Модель вкладки в каталоге
 **/
class reflex_editor_rootTab extends reflex {

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
				  'name' => 'icon',
				  'type' => 'file',
				),
			),
		);
	}
	
	public static function all() {
	    return reflex::get(get_class());
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
        foreach(reflex_editor_root::level0() as $root) {
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
	    return reflex::create(get_class(),$p);
	}
	
	public function removeAll() {
	    return self::all()->delete();
	}
	
	public function icon() {
	    return $this->pdata("icon");
	}
	
}
