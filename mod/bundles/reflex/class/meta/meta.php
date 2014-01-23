<?

/**
 * Контроллер управления мета-данными через админку
 **/
class reflex_meta extends mod_controller {

	private $reflex;
	public function __construct($reflex=null) {
		return $this->reflex = $reflex;
	}

	public function hash() {
		return get_class($this->reflex).":".$this->reflex->id();
	}

	/**
	 * На свякий случай, ограничиваем доступ к контроллерам метаданных только для
	 * зарегистрированных пользователей
	 **/
	public static function postTest() {
		return user::active()->checkAccess("admin:showInterface");
	}

	/**
	 * Контроллер получения метаданных объекта
	 **/
	public static function post_get($p) {
	
		$editor = reflex_editor::byHash($p["index"]);
		$item = $editor->item()->metaObject();
		
		if(!$editor->beforeView()) {
			mod::msg("У вас нет доступа для просмотра метаданных",1);
			return fasle;
		}
		
		if($item->exists()) {
		
			$data = $item->editor()->inxForm();
			
			return array(
				"form" => $data,
			);
			
		} else {
		    return array(
		        "error" => "У данного объекта отсутствуют метаданные."
			);
		}
		
	}

	/**
	 * Контроллер сохранения метаданных
	 **/
	public static function post_save($p) {
	
	    $editor = reflex_editor::byHash($p["index"]);

		if(!$editor->beforeEdit()) {
		    mod::msg("Вы не можете редактировать метаданные этого объекта",1);
		    return;
		}

		$editor->saveMeta($p["data"],$p["lang"]);

		mod::msg("Мета: данные сохранены");
	}

	/**
	 * Контроллер удаления метаданных
	 **/
	public static function post_delete($p) {
	
	    $editor = reflex_editor::byHash($p["index"]);

		if(!$editor->beforeEdit()) {
		    mod::msg("Вы не можете удалить метаданные этого объекта",1);
		    return;
		}

		$editor->deleteMeta($p["lang"]);
		
		mod::msg("Данные удалены");
	}

}
