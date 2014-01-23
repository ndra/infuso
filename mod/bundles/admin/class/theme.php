<?

/**
 * Стандартная тема модуля admin
 **/

class admin_theme extends tmp_theme {

	/**
	 * @return Приоритет темы =-1
	 **/
	public function priority() {
		return -1;
	}

	public function path() {
		return "/admin/theme/";
	}

	public function base() {
		return "admin";
	}

	public function autoload() {
		return true;
	}

	public function name() {
		return "admin";
	}

}
