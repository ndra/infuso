<?

/**
 * Обработчик событий
 **/
class user_social_handler implements mod_handler {

	public function on_mod_beforeAction() {

		user_social::appendToActiveUser();

	}

}
