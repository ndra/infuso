<?

namespace infuso\admin\utils;

/**
 * Стандартная тема модуля admin
 **/
class theme extends \tmp_theme {

	/**
	 * @return Приоритет темы =-1
	 **/
	public function priority() {
		return -1;
	}

	public function path() {
		return self::bundle()->path()."/theme/";
	}

	public function base() {
		return "admin/utils";
	}

	public function autoload() {
		return true;
	}

	public function name() {
		return "utils";
	}

}
