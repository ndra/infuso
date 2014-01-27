<?

class realty_flat_type extends reflex {

	public static function all() { return reflex::get(get_class()); }
	public static function get($id) { return reflex::get(get_class(),$id); }
	public static function reflex_root() {
		return array(
			self::all()->title("Справочник типов квартир")->param("tab","system")
		);
	}

}
