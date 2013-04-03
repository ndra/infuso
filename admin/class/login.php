<?

class admin_login extends mod_controller {

	public static function postTest() {
		return true;
	}

	/**
	 * Контроллер получения информации о логине пользователя
	 * Используется в inx-форме входа
	 **/
	public static function post_info($p) {
	
	    $url = new mod_url($p["url"]);

		$acccess = false;
		if($action = $url->action()) {
			$access = $action->test();
		}

	    return array(
	        "user" => array(
				"email" => user::active()->data("email"),
			),
	        "superadmin" => mod_superadmin::check(),
	        "access" => $access,
	    );
	}

	public static function post_standartLogin($p) {
	    user::login($p["email"],$p["password"],$p["keep"]);
	    return self::post_info($p);
	}

	public static function post_standartLogout($p) {
	    user::logout();
	    return self::post_info($p);
	}

	public static function post_superadminLogout($p) {
	    mod_superadmin::logout();
	    return self::post_info($p);
	}

	public static function post_superadminLogin($p) {
	    mod_superadmin::login($p["password"]);
	    return self::post_info($p);
	}

}
