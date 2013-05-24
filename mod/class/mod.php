<?

class mod extends mod_controller {

	private static $info = array();

	private static $debug = null;
	
	private static $extends = array();

	public static function indexTest() {
		return true;
	}
	
	public static function index() {
	    
	    try {
			mod_console::xindex();
		} catch (Exception $ex) {
		    echo "Exception: ".$ex->getMessage();
		}

	}
	
	/**
	 * Возвращает параметр из файла конфигурации модуля info.ini
	 * Первый параметр - модуль
	 * Второй параметр - имя связанного модуля
	 * Третий параметр - ключ, значение которого нужно вернуть
	 * Возвращает переменную или массив, в зависимости от того что содержится в файле info.ini
	 **/
	public static function info($module,$p1,$p2) {

	    if($module==null) {
	        $ret = array();
	        foreach(mod::all() as $mod) {
				if($values = mod::info($mod,$p1,$p2)) {
					if(!is_array($values)) $values = array($values);
					foreach($values as $v) $ret[] = $v;
				}
	        }
	        return $ret;
	    }

		if(!self::$info[$module]) {
	    	self::$info[$module] = mod_file::get("/$module/info.ini")->ini(true);
	    }

		return self::$info[$module][$p1][$p2];
	}

	private static $modules = null;

	/**
	 * Возвращает список всех модулей
	 **/
	public static function all() {
		if(!self::$modules) {
		    foreach(mod_file::get("/")->dir()->folders() as $folder) {
		        self::$modules[] = $folder->name();
		    }
		}
	    return self::$modules;
	}

	/**
	 * Возвращает путь к корню сайта в файловой системе сервера
	 * Используется функциями модуля file для перевода путей ФС в абсолютные
	 **/
	public static function root() {
	    return $_SERVER["DOCUMENT_ROOT"]."/";
	}

	/**
	 * Возвращает параметр конфигурации
	 * той, что в /mod_conf/
	 **/
	public static function conf($key) {
	    return mod_conf::get($key);
	}

	/**
	 * Включен ли режим отладки
	 **/
	public function debug() {
	
	    if(self::$debug===null) {
	    
	        self::$debug = false;
	
		    if(!mod_conf::get("mod:debug")) {
				self::$debug = false;
				return self::$debug;
		    }

		    if(!mod_superadmin::check()) {
		        self::$debug = false;
				return self::$debug;
		    }
		    
		    self::$debug = true;
			return self::$debug;
		        
		}
	
		return self::$debug;
	}

	/**
	 * @return Возвращает список всех классов
	 * @return Если указан параетр extends, возвращает список всех классов, расширяющих extends
	 **/
	public static function classes($extends=null) {

		$ret = self::classmap();
		$ret = $ret["map"];

		if(!$ret)
		    $ret = array();

		if($extends) {

		    if(!array_key_exists($extends,self::$extends)) {
				self::$extends[$extends] = array();
		        foreach($ret as $key=>$classProos)
		            if(in_array($extends,$classProos["p"]) && !$classProos["a"])
		                self::$extends[$extends][] = $key;
		    }

		    return self::$extends[$extends];

		}

		return $ret;
	}

	/**
	 * @return Один параметр - проверяет класс на наличие
	 * @return Два параметра - проверяет класс на наличие и на то что он расширяет $extends
	 **/
	public static function testClass($class,$extends=null) {

		if($class=="mod" && $extends=="mod_controller")
		    return true;

	    $classes = self::classmap("map");
	    if(!$classes)
	        return;

		if(!array_key_exists($class."",$classes))
			return false;

		if($extends) {
			if(!in_array($extends,$classes[$class]["p"]) && $extends!=$class)
			    return false;
		}
		    
		return true;
	}

	private static $classmap = null;

	/**
	 * Возвращает карту классов
	 * Если передан параметр, вернет часть карты классов
	 **/
	public static function classmap($key=null) {

		// Загружаем карту класса по требованию
		if(!self::$classmap) {
		    $f = mod_file::get("/mod/service/classmap.inc.php");
		    if($f->exists())
		    	self::$classmap = $f->inc();
		}

	    if(!self::$classmap)
		    self::$classmap = array();

		$ret = self::$classmap;

		if($key)
		    $ret = $ret[$key];

		return $ret;
	}

	public static function setClassMap($map) {
		self::$classmap = $map;
	}

	public static function call($name) {

		$name = strtr($name,array("::"=>":"));

		$p = array();
		for($i=1;$i<func_num_args();$i++) {
		    $p[] = func_get_arg($i);
		}

		list($class,$method) = explode(":",$name);
		return call_user_func_array(array($class,$method),$p);
	}

	/**
	 * @return Возвращает случайный хэш длины $length
	 **/
	public static function id($length=30) {
		$chars = "1234567890qwertyuiopasdfghjklzxcvbnm";
		$ret = "";
		for($i=0;$i<$length;$i++) {
		    $ret.= $chars[rand()%strlen($chars)];
		}
		return $ret;
	}

	public static function loadXMLConf($doc) {
	    $driver = new mod_confLoader_xml();
	    $php = $driver->read($doc);
	    return $php;
	}

	public static function saveXMLConf($targetFile,$php) {
	    $driver = new mod_confLoader_xml();
	    $xml = $driver->write($php);
		mod_file::get($targetFile)->put($xml);
	}

	/**
	 * Выводит XML елочкой в строку
	 **/
	public static function prettyPrintXML($xml,$root=1) {

		if(get_class($xml)=="SimpleXMLElement")
			$xml = dom_import_simplexml($xml);


	    $ret = array();
		switch($xml->nodeType) {
		    case 9:
				$ret = self::prettyPrintXML($xml->firstChild,0);
				break;
			case 1:
			    $start = '<'.$xml->nodeName;
			    $attr = array();
				foreach($xml->attributes as $attribute)
			        $attr[] = $attribute->nodeName."="."'".htmlspecialchars($attribute->nodeValue,ENT_QUOTES)."'";
		        $start.= sizeof($attr) ? " ".implode(" ",$attr)." " : "";
				$start.=">";

			    if($xml->childNodes->length==1 & $xml->firstChild->nodeType==3) {
			    	$ret[] = $start.htmlspecialchars($xml->firstChild->nodeValue,ENT_QUOTES).'</'.$xml->nodeName.'>';
			    }
			    else {
			        $ret[] = $start;
				    foreach($xml->childNodes as $child)
				        foreach(self::prettyPrintXML($child,0) as $str)
				            $ret[] = "\t".$str;
				    $ret[] = '</'.$xml->nodeName.'>';
			    }
			    break;
			case 3:
			    if(trim($xml->nodeValue))
			    	$ret[] = htmlspecialchars(trim($xml->nodeValue),ENT_QUOTES);
			    break;
		}

		if(!$root) return $ret;
		else return implode("\n",$ret);
	}

	/**
	 * Подключает библиотеку core.js
	 **/
	public static function coreJS() {
        tmp::jq();
		tmp::singlejs("/mod/res/core.js",-900);
	}

	/**
	 * Выводит сообщение
	 **/
	public function msg($message,$error=false) {
		mod_log::msg($message,$error);
	}

	/**
	 * Заносит сообщение в лог
	 **/
	public function trace($message) {
		mod_log::trace($message);
	}

	/**
	 * Возвращает экшн (класс mod_action)
	 **/
	public static function action($a,$b=null,$c=array()) {
		return mod_action::get($a,$b,$c);
	}

	/**
	 * Создает и возвращает экземпляр класса mod_event
	 **/
	public function event($eventName,$params=array()) {
		return new mod_event($eventName,$params);
	}

	/**
	 * Вызывает событие
	 * @param string $eventName Имя события
	 * @param array $params Параметры события
	 **/
	public function fire($eventName,$params=array()) {
		$event = self::event($eventName,$params);
		$event->fire();
		return $event;
	}

	/**
	 * Обертка для mod_cooke::set() и mod_cookie::get()
	 **/
	public static function cookie($key,$val=null) {
  		if(func_num_args()==1) {
		    return mod_cookie::get($key);
		}
		if(func_num_args()==2) {
	    	mod_cookie::set($key,$val);
	    }
	}

	public static function session($key,$val=null) {
		if(func_num_args()==1) {
		    return mod_session::get($key);
		}
	    mod_session::set($key,$val);
	}

	/**
	 * @return object mod_url url реферера
	 **/
	public function referer() {
		return mod_url::get($_SERVER["HTTP_REFERER"]);
	}

	/**
	 * @param $url string
	 * @return object mod_url При вызове без параметра, вернет текущий урл
	 **/
	public function url($url=null) {
		if(func_num_args()==0) {
		    return mod_url::current();
		}
		return mod_url::get($url);
	}

	/**
	 * @param $class string
	 * @return Создает и возвращает поле соответствующего класса
	 * Вместо класса может использоваться короткий илиас, например "checkbox"
	 **/
	public function field($class) {
		return mod_field::get($class);
	}
	
	/**
	 * Возвращает службу по ее имени
	 **/
	public function service($serviceName) {
	    return mod_service::get($serviceName);
	}
	
	function base64URLEncode($data) {
		return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
	}

	function base64URLDecode($data) {
		return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
	}
	
	public static function splitAndTrim($str,$separator) {
        $ret = array();
        foreach(explode($separator,$str) as $part)
            if(trim($part))
                $ret[] = $part;
        return $ret;
    } 	

}
