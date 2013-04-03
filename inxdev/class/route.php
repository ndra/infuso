<?

/**
 * Роутер, работающий с базой данных.
 * Отвечает за то что мы видем в каталоге в разделе «Роуты»
 **/

class inxdev_route extends mod_route {

	/**
	 * url => action
	 **/
	public function forward($url) {
	
		$path = $url->path();
		
		if(!preg_match("/^\/inxdev\/example([a-z0-9\_\-\/]*)$/",$path,$matches))
			return;
			
		
		return mod::action("inxdev","index",array("page"=>$matches[1]));
		

	}

	/**
	 * Отображение action => url
	 * Используется системой при построении url
	 **/
	public function backward($controller) {


	}

}
