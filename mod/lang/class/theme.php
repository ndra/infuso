<?

/**
 * Стандартная тема для интернет-магазина
 **/

class lang_theme extends tmp_theme {

	/**
	 * @return Приоритет темы =-1
	 **/
	public function priority() {
		return -1;
	}

	public function path() {
		return "/lang/theme/";
	}

	public function base() {
		return "lang";
	}

	public function autoload() {
		return true;
	}

	public function name() {
		return "Стандартная тема lang";
	}

}
