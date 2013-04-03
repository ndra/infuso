<?

class mod_conf_main extends mod_conf {

	public function name() {
	    return "mod";
	}

	/**
	 * Возвращает все параметры конфигурации модуля mod
	 **/
	public function conf() {
	    return array(

	        array(
	            "title"=>"URL обновления",
				"id"=>"mod:updateURL",
			),
	        array(
	            "title"=>".htaccess",
				"id"=>"mod:htaccess",
				"type"=>"textarea"
			),
	        array (
	            "title"=>"Шаблон ошибки 404",
				"id"=>"mod:404",
			),
	        array(
	            "title"=>"Название сайта",
				"id"=>"mod:site_title",
			),
	        array(
	            "title"=>"Административная почта",
				"id"=>"mod:admin_email",
			),
	        array(
	            "title"=>"Редирект с www на без www",
				"id"=>"mod:htaccess-non-www",
				"type"=>"checkbox"
			),
	        array(
				"id"=>"mod:cacheDriver",
				"title"=>"Кэширующая система",
				"type"=>"select",
				"values"=>array(
	            "filesystem" => "Файловая система",
	            "xcache" => "xCache",
			)),
	        array(
				"id"=>"mod:cacheURL",
				"title"=>"Кэшировать url",
				"type"=>"checkbox"
			),
	        array(
				"id"=>"mod:debug",
				"title"=>"Режим отладки",
				"type"=>"checkbox"
			),
	        
		);
	}

}
