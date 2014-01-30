<?

/**
 * Стандартная тема модуля comment
 **/

class comment_theme extends tmp_theme {

	public function path() {
		return "/comment/theme/";
	}

	public function base() {
		return "comment";
	}

	public function autoload() {
		return true;
	}

	public function name() {
		return "comment";
	}

}
