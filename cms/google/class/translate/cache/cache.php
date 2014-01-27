<?

class google_translate_cache extends reflex {

	public static function all() {
	    return reflex::get(get_class());
	}

	public static function get($id) {
	    return reflex::get(get_class(),$id);
	}

	public function reflex_root() {
	    return array(
	        self::all()->title("Перевод Google")->param("tab","system"),
	    );
	}
	
}
