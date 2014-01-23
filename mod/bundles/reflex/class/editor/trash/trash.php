<?

class reflex_editor_trash extends reflex {

	public static function get($id) {
		return reflex::get(get_class(),$id);
	}
	
	public static function all() {
		return reflex::get(get_class())->desc("datetime");
	}

	public static function reflex_root() {
		if(mod_superadmin::check())
			return self::all()->icon("bin")->title("Удаленные объекты")->param("tab","system");
		return array();
	}

	public function reflex_beforeCreate() {
		$this->data("datetime",util::now());
	}

	public function reflex_repairClass() {
		return false;
	}
	
}
