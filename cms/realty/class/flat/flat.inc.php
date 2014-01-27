<? class realty_flat extends reflex {

// ---------------------------------------------------------------------

public static function indexTest() { return true; }
public static function index() {
    tmp::exec("realty:flats");
}

// ---------------------------------------------------------------------

public static function all() { return reflex::get(get_class()); }
public static function get($id) { return reflex::get(get_class(),$id); }
public static function reflex_root() { return array(
	self::all()->title("Квартиры")->param("tab","system")
); }

} ?>
