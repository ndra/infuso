<?

/**
 * Модель-заглушка
 * используется при попытке создать объект несуществующего класса
 **/
class reflex_none_editor extends reflex_editor {

	public function itemClass() {
		return "reflex_none";
	}
	
	public function renderItemData() {
	
	    return array(
	        "text" => "reflex_none",
		);
	}

}
