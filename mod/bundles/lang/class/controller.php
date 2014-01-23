<?

class lang_controller extends mod_controller {

	/**
	 * Включаем индекс
	 * @return true
	 **/
	public function indexTest() {
		return true;
	}

	/**
	 * Включаем POST
	 * @return true
	 **/
	public function postTest() {
		return true;
	}

	/**
	 * @return список языков
	 **/
	public static function post_getAll() {
		$ret = array();
		foreach(lang::all() as $lang)
		    $ret[$lang->id()] = $lang->data("name");
		return $ret;
	}

	/**
	 * экшн установки активного языка
	 **/
	public static function post_set($p) {
		lang::get($p["lang"])->activate();
	}

	/**
	 * экшн установки активного языка
	 **/
	public static function index_set($p) {
		lang::get($p["lang"])->activate();
		$back = $_SERVER["HTTP_REFERER"];
		header("location:$back");
	}

}
