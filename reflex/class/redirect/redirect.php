<?

class reflex_redirect extends reflex {

	public function indexTest() {
		return true;
	}

	public function index_redirect($p) {
		header("Location: $p[target]", true, 301);
	}

	public function reflex_title() {
		return $this->data("source")." &rarr; ".$this->data("target");
	}

	public static function reflex_root() {
	    return array(
			self::all()->title("Редиректы")->param("tab","system"),
		);
	}

	public function reflex_meta() {
		return false;
	}

	public function reflex_route() {
		return false;
	}

	public static function all() {
		return reflex::get(get_class());
	}
	
}
