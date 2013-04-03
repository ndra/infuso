<?

/**
 * Модель города
 **/
class geo_city extends reflex {

	public static function all() {
	    return reflex::get(get_class())->asc("title");
	}

	public static function get($id) {
		return reflex::get(get_class(),$id);
	}

	public static function reflex_root() {
	    return self::all()->title("Все города")->param("tab","system");
	}

	public function region() {
		return $this->pdata("regionID");
	}

	public function reflex_parent() {
		return $this->region();
	}

	public function byName($title) {
		return self::all()->eq("title",$title)->one();
	}

	/**
	 * Возвращает координаты города
	 **/
	public function coords() {
		return geo_coder_yandex::getCoords($this->title());
	}
	
}
