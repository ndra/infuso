<?

/**
 * Стандартная тема для интернет-магазина
 **/

class ndra_theme extends tmp_theme {

/**
 * @return Приоритет темы =-1
 **/
public function priority() {
	return -1;
}

public function path() {
	return "/ndra/theme/";
}

public function base() {
	return "ndra";
}

public function autoload() {
	return true;
}

public function name() {
	return "Стандартная тема ndra";
}

}
