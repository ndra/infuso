<?

/**
 * Служебный класс - обработчик событий
 **/
class user_handler implements mod_handler {

	public function on_mod_beforeAction() {
	    user::active()->registerActivity();
	}


}
