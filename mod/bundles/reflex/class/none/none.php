<?

/**
 * Модель-заглушка
 * используется при попытке создать объект несуществующего класса
 **/
class reflex_none extends reflex{

	public static function reflex_table() {
		return null;
	}
	
	public function reflex_title() {
	    return "non-existing class";
	}

}
