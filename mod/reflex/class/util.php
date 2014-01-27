<?

class reflex_util {

	/**
	 * Возврашает имя класса reflex для данного класса
	 * Использовалось раньше для переопределения классов
	 * Можно было сделать так чтобы reflex::get("user") возвращало бы объект другого класса
	 * С появлением поведений необходимоть с это отпала
	 * Метод вернет reflex_none при обращении к классу, которого не существует
	 **/
	public static function getItemClass($class) {

	    if(!mod::app()->service("classmap")->testClass($class,"reflex")) {
			return "reflex_none";
		}

		return $class;
	}

	public static function getListClass($class) {
	    return "reflex_collection";
	}

	public static function itemFromURL($url) {
	
	    $action = mod_action::forwardTest(mod_url::get($url));

	    if(!$action) {
	        return reflex::get("reflex_none",0);
	    }

	    if($action->action()!="item") {
	        return reflex::get("reflex_none",0);
	    }


	    $params = $action->params();
	    return reflex::get($action->className(),$params["id"]);
	}

}
