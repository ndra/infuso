<?

/**
 * Класс для сохранения модели формы в базу
 **/
class form_validate extends reflex {

	public static function postTest() {
		return true;
	}

	public static function post_validate($p) {

	    $hash = $p["hash"];
	    $form = self::get($hash)->form();

	    $valid = $form->validate($p["data"]);

		ob_start();
		tmp::exec("form:msg",$form->error());
		$html = ob_get_clean();

	    return array(
	        "valid" => $valid,
	        "name" => $form->errorName(),
	        "html" => $html,
	    );
	}

	/**
	 * Возвращает модель формы
	 **/
	public function form() {
		return form::unserialize($this->data("formData"));
	}

	/**
	 * Возвращает коллекцию всех моделей формы
	 **/
	public static function all() {
		return reflex::get(get_class())->desc("created");
	}

	/**
	 * Возвращает сохраненную формы по хэшу
	 **/
	public static function get($hash) {
		return self::all()->eq("hash",$hash)->one();
	}

	public function reflex_cleanup() {
		return true;
	}

	public function reflex_root() {
		return self::all()->title("Валидация форм")->param("tab","system");
	}
	
}
