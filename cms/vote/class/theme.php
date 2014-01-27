<?

/**
 * Стандартная тема для интернет-магазина
 **/

class vote_theme extends tmp_theme {

	/**
	 * @return Приоритет темы =-1
	 **/
	public function priority() {
		return -1;
	}

	public function path() {
		return "/vote/theme/";
	}

	public function base() {
		return "vote";
	}

	public function autoload() {
		return true;
	}

	public function name() {
		return "Стандартная тема vote";
	}

}
