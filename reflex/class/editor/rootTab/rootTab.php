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
	
	public static function get($id) {
	    return reflex::get(get_class(),$id);
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
