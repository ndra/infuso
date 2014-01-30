<?

/**
 * Стандартная тема модуля reflex
 **/

class reflex_theme extends tmp_theme {

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
		return "reflex";
	}

	public function autoload() {
		return true;
	}

	public function name() {
		return "Стандартная тема reflex";
	}

}
