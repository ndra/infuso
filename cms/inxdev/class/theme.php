<?

/**
 * Стандартная тема для интернет-магазина
 **/

class inxdev_theme extends tmp_theme {

	public function path() {
		return mod::service("classmap")->getClassBundle(get_class())->path()."/theme";
	}

	public function base() {
		return "inxdev";
	}

	public function autoload() {
		return true;
	}

	public function name() {
		return "Стандартная тема inxdev";
	}

}
