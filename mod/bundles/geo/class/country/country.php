<?

/**
 * Модель страны для модуля geo
 **/
class geo_country extends reflex {

	public static function all() {
	    return reflex::get(get_class())->asc("title");
	}

	public static function get($id) {
		return reflex::get(get_class(),$id);
	}

	public static function reflex_root() {
	    return self::all()->title("Все страны")->param("tab","system");
	}

	/**
	 * Возвращает коллекцию регионов для страны
	 **/
	public function regions() {
		return geo_region::all()->eq("countryID",$this->id());
	}

	public function reflex_children() {
		return array(
			$this->regions()->title("Регионы"),
		);
	}
	
}

