<?

/**
 * Роутер, работающий с базой данных.
 * Отвечает за то что мы видем в каталоге в разделе «Роуты»
 **/

class reflex_route extends mod_route implements mod_handler{

	public function priority() {
		return 100;
	}
	
	/**
	 * Возвращает коллекцию роутов для текущего домена
	 **/
	public function routesForActiveDomain() {
	    $domain = reflex_domain::active()->id();
	    return reflex_route_item::all()->where("!`domain` or `domain`='$domain' ");
	}

	/**
	 * Возвращает коллекцию всех роутов
	 **/
	public static function allRoutes() {
	    // Ограничение в 100 роутов нужно чтобы система не впала в кому в случае ошибки :)
	    return self::routesForActiveDomain()->eq("seek","")->limit(100);
	}

	/**
	 * url => action
	 **/
	public function forward($url) {

	    $route = self::routesForActiveDomain()->eq("url",$url->path())->one();
	    if($route->exists()) {
	        $params = $url->query();
	        $params = array_merge($params,$route->pdata("params"));
	        list($class,$action) = explode("/",$route->data("controller"));
	        tmp::obj($route);
	        return mod_action::get($class,$action,$params);
	    }

	    foreach(self::allRoutes() as $route) {
	        self::$keys = array();
	        $r = $route->data("url");
	        $r = preg_replace_callback("/\<([a-z0-9]+)\:(.*?)\>/s",array("self","replace"),$r);
	        $r = preg_quote($r);
	        $r = "<^".preg_replace_callback("/#(\d+)#/s",array("self","replaceBack"),$r).'$>';
	        if(preg_match($r,$url->path(),$matches,PREG_OFFSET_CAPTURE)) {
	            array_shift($matches);
	            $values = array();
	            foreach($matches as $m) {
	                $key = $m[1];
	                $val = $m[0];
	                if(strlen($val)>strlen($ret[$key]))
	                    $values[$key] = $val;
	            }
	            if(sizeof(self::$keys))
	                $params = array_combine(self::$keys,$values);
	            else
	                $params = array();

	            $params = array_merge($url->query(),$params);
	            $params = array_merge($params,$route->pdata("params"));
	            list($class,$action) = explode("/",$route->data("controller"));
	            tmp::obj($route);
	            return mod_action::get($class,$action,$params);
	        }
	    }
	}

	private static $n = 0;
	private static $reg = array();
	private static $keys = array();
	private function replace($a) {
	    self::$n++;
	    self::$keys[self::$n] = $a[1];
	    self::$reg[self::$n] = $a[2];
	    return "#".self::$n."#";
	}

	private function replaceBack($a) {
	    return "(".self::$reg[$a[1]].")";
	}

	/**
	 * Отображение action => url
	 * Используется системой при построении url
	 **/
	public function backward($controller) {
	
		// Пытаемся получить url по запросу в базу
		// Это сработет для статических url, без параметров
		$seek = $controller->hash();
	    $route = self::routesForActiveDomain()->eq("seek",$seek)->one();
	    if($route->exists())
	        if($url=$route->testController($controller))
	            return $url;

		// Если быстрый способ не сработал, перебираем все роуты и ищем подходящий
	    foreach(self::allRoutes() as $route) {
	        if($url = $route->testController($controller))
	            return $url;
	    }
	}

	/**
	 * Вызывается до старта контроллера
	 * Если вызван метод className::item класса, наследуемого от reflex,
	 * то устанавливам текущий объект tmp::obj
	 **/
	public static function on_mod_beforeAction($p) {
	
		$action = $p->param("action");
	    if($action->action()=="item") {
			$id = $action->param("id");
			$obj = reflex::get($action->className(),$id);
			if(!$obj->published()) {
				mod_cmd::error(404);
			}
			tmp::obj($obj);
		}

		$action = tmp::obj()->meta("beforeAction");
		eval($action);
	}

}
