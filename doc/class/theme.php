<?

/**
 * Стандартная тема для интернет-магазина
 **/

class doc_theme extends tmp_theme {

	public function path() {
		return "/doc/theme/";
	}

	public function base() {
		return "doc";
	}

	public function autoload() {
		return true;
	}

	public function name() {
		return "Стандартная тема doc";
	}

}
