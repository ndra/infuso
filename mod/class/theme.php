<?

/**
 * Стандартная тема для модуля mod
 **/

class mod_theme extends tmp_theme {

public function path() {
	return "/mod/theme/";
}

public function base() {
	return "mod";
}

public function autoload() {
	return true;
}

public function name() {
	return "Стандартная тема mod";
}

}
