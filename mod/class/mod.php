<?

namespace infuso\core;

class mod extends controller {

	private static $info = array();

	private static $debug = null;
	
	private static $extends = array();

	public static function indexTest() {
		return true;
	}
	
	public static function index() {
	    
	    try {
			\infuso\core\console::xindex();
		} catch (Exception $ex) {
		    echo "Exception: ".$ex->getMessage();
		}

	}
	
	private static $modules = null;

	/**
	 * Возвращает путь к корню сайта в файловой системе сервера
	 * Используется функциями модуля file для перевода путей ФС в абсолютные
	 **/
	public static function root() {
	    return $_SERVER["DOCUMENT_ROOT"]."/";
	}

	/**
	 * Возвращает параметр конфигурации
	 * той, что в /mod/conf/
	 **/
	public static function conf($key) {
	    return conf::get($key);
	}

	/**
	 * Включен ли режим отладки
	 **/
	public function debug() {
	
	    if(self::$debug===null) {
	    
	        self::$debug = false;
	
		    if(!conf::get("mod:debug")) {
				self::$debug = false;
				return self::$debug;
		    }

		    if(!superadmin::check()) {
		        self::$debug = false;
				return self::$debug;
		    }
		    
		    self::$debug = true;
			return self::$debug;
		        
		}
	
		return self::$debug;
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
	    $driver = new \mod_confLoader_xml();
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
		log::msg($message,$error);
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
		return action::get($a,$b,$c);
	}

	/**
	 * Создает и возвращает экземпляр класса mod_event
	 **/
	public function event($eventName,$params=array()) {
		return new event($eventName,$params);
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
		return \mod_field::get($class);
	}
	
	/**
	 * Возвращает службу по ее имени
	 **/
	public function service($serviceName) {
	    return mod::app()->service($serviceName);
	}
	
	function base64URLEncode($data) {
		return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
	}

	function base64URLDecode($data) {
		return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
	}
	
	public static function splitAndTrim($str,$separator) {
        $ret = array();
        foreach(explode($separator,$str) as $part) {
            if(trim($part)!=="") {
                $ret[] = $part;
            }
        }
        return $ret;
    }

	/**
	 * Возвращает текущее приложение
	 **/
	public function app() {
	    return \infuso\core\app::current();
	}

}
