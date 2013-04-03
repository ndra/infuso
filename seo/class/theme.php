<?

/**
 * Стандартная тема для модуля seo
 **/

class seo_theme extends tmp_theme {

public function path() {
	return "/seo/theme/";
}

public function base() {
	return "seo";
}

public function autoload() {
	return true;
}

public function name() {
	return "Стандартная тема seo";
}

}
