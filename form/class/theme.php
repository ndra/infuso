<?

/**
 * Стандартная тема для интернет-магазина
 **/

class form_theme extends tmp_theme {

/**
 * @return Приоритет темы =-1
 **/
public function priority() {
	return -1;
}

public function path() {
	return "/form/theme/";
}

public function base() {
	return "form";
}

public function autoload() {
	return true;
}

public function name() {
	return "form";
}

}
