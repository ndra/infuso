<?

class google_conf extends mod_conf {

	public function name() {
		return "Google";
	}

	/**
	 * Возвращает все параметры конфигурации
	 **/
	public function conf() {
	    return array(
	        array(
				"id" => "google:key",
				"title" => "Ключ API",
			),
	    );
	}

}
