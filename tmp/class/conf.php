<?

class tmp_conf extends mod_conf {

	public function name() {
		return "tmp";
	}

	/**
	 * Возвращает все параметры конфигурации
	 **/
	public function conf() {
	    return array(
	        array(
				"id" => "tmp:always-render",
				"title" => "Не кэшировать стили и скрипты",
				"type" => "checkbox",
			),array(
				"id" => "tmp:lesscss",
				"title" => "Использовать lesscss",
				"type" => "checkbox"
			),
	    );
	}

}
