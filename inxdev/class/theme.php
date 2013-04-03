<?

/**
 * Стандартная тема для интернет-магазина
 **/

class inxdev_theme extends tmp_theme {

public function path() {
	return "/inxdev/theme/";
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
