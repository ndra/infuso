<?

/**
 * Стандартная тема для интернет-магазина
 **/

class util_theme extends tmp_theme {

	/**
	 * @return Приоритет темы =-1
	 **/
	public function priority() {
		return -1;
	}

	public function path() {
		return "/util/theme/";
	}

	public function base() {
		return "util";
	}

	public function autoload() {
		return true;
	}

	public function name() {
		return "util";
	}

}
