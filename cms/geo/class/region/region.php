<?

/**
 * Модель региона для модуля geo
 **/
class geo_region extends reflex {

	public static function all() {
	    return reflex::get(get_class())->asc("title");
	}

	public static function get($id) {
		return reflex::get(get_class(),$id);
	}

	public static function reflex_root() {
	    return self::all()->title("Все регионы")->param("tab","system");
	}

	public function country() {
		return $this->pdata("countryID");
	}

	public function reflex_parent() {
		return $this->country();
	}
	
	public function byName($title) {
		return self::all()->eq("title",$title)->one();
	}

	public function cities() {
		return geo_city::all()->eq("regionID",$this->id());
	}

	public function reflex_children() {
		return array(
			$this->cities()->title("Города"),
		);
	}
	
}
