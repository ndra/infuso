<?

/**
 * Модель авторизации пользователя
 **/
class user_auth extends reflex {

    /**
	 * Возвращает все авторизации всех пользователей
 	**/
	public static function all() {
		return reflex::get(get_class())->desc("time");
	}

	public static function get($id) {
		return reflex::get(get_class(),$id);
	}

	public function user() {
		return user::get($this->data("userID"));
	}

	public function reflex_beforeCreate() {
		$this->data("time",util::now());
	}

}
