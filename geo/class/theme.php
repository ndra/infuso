<?

/**
 * Стандартная тема модуля geo
 **/
class geo_theme extends tmp_theme {

	/**
	 * @return Приоритет темы =-1
	 **/
	public function priority() {
		return -1;
	}

	public function path() {
		return "/geo/theme/";
	}

	public function base() {
		return "geo";
	}

	public function autoload() {
		return true;
	}

	public function name() {
		return "geo";
	}

}
