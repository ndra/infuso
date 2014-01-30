<?

/**
 * Стандартная тема для интернет-магазина
 **/

class user_theme extends tmp_theme {

	/**
	 * @return Приоритет темы =-1
	 **/
	public function priority() {
		return -1;
	}

	public function path() {
		return self::inspector()->bundle()->path()."/theme/";
	}

	public function base() {
		return "user";
	}

	public function autoload() {
		return true;
	}

	public function name() {
		return "user";
	}

}
