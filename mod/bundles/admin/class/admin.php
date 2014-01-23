<?

class admin extends mod_controller {

	private static $showLogin = true;

	public static function fuckoff() {
	    tmp::noindex();
		if(self::$showLogin) {
		    if(user::active()->checkAccess("admin:showInterface")) {
		        header("Location:/admin/");
		        die();
			} else {
	    		tmp::exec("admin:not_logged_in");
			}
		} else {
			mod::app()->httpError(404);
		}
	}

	public static function indexTest() {
		return user::active()->checkAccess("admin:showInterface");
	}

	public static function index($p1=null) {
		admin::header("Администрирование");
		tmp::exec("admin:startPage");
		tmp::exec("admin:footer");
	}

	public static function indexTitle(){
		return "Администрирование";
	}

	public static function indexFailed() {
		admin::fuckoff();
	}

	/**
	 * Выводит шапку админки
	 **/
	public static function header($title="") {
	    tmp::noindex();
		tmp::param("title",$title);
		tmp::param("back-end",1);
		tmp::exec("/admin/header");
	}

	/**
	 * Выводит подвал админки
	 **/
	public static function footer() {
		tmp::exec("/admin/footer");
	}

	/**
	 * Возвращает все параметры конфигурации
	 **/
	public static function configuration() {
	    return array(
	        array("id"=>"admin:secretURL","title"=>"Секретный ключ url"),
	        array("id"=>"admin:showMenu","type"=>"checkbox","title"=>"Показывать меню администратора на всех страницах"),
		);
	}

	/**
	 * Вызывает виджет горизонтального администраторского меню
	 **/
	public static function menu($always=false) {
		$obj = tmp::obj();
		if(tmp::param("admin-header")) return;
		if(!$always) {
			if(!$obj->exists()) return;
			if(!$obj->editor()->beforeView()) return;
		}
		tmp::exec("admin:menu");
		tmp::param("admin-header",true);
	}

}
