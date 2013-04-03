<? class ndra_snow extends mod_controller {

public static function indexTest() { return true; }
public static function index() {
    tmp::header();
    
	self::auto();
    
    tmp::footer();
}

// ----------------------------------------------------------------------------------------------

public static function add() {
	tmp::js("/ndra/res/snow/snow.js");
}

public static function auto() {
	$day = date("z");
	if($day>365-14 || $day<14) self::add();
}

} ?>
