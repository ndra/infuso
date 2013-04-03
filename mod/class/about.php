<?

class mod_about extends mod_controller {

	public static function indexTest() {
		return user::active()->checkAccess("admin:showInterface");
	}
	
	public static function indexFailed() {
		return admin::fuckoff();
	}
	
	public static function indexTitle() {
		return "Лицензионное соглашение";
	}
	
	public static function index() {
		tmp::exec("/mod/about");
	}

}
