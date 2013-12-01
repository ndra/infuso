<?

class mod_classmap_service extends mod_service {

	private static $extends = array();
	
	private static $classmap = null;

	public function defaultService() {
	    return "classmap";
	}
	
	public function classes($extends = null) {
	    return $this->getClassesExtends($extends);
	}
	
	/**
	 * @return Возвращает список всех классов
	 * @return Если указан параетр extends, возвращает список всех классов, расширяющих extends
	 **/
	public function getClassesExtends($extends=null) {

		$ret = self::classmap();
		$ret = $ret["map"];

		if(!$ret) {
		    $ret = array();
		}

		if($extends) {

		    if(!array_key_exists($extends,self::$extends)) {
				self::$extends[$extends] = array();
		        foreach($ret as $key=>$classProos) {
		            if(in_array($extends,$classProos["p"]) && !$classProos["a"]) {
		                self::$extends[$extends][] = $key;
					}
				}
		    }

		    return self::$extends[$extends];

		}

		return $ret;
	}
	
	public static function testClass($class,$extends=null) {
	
		if($class=="mod" && $extends=="mod_controller") {
		    return true;
        }

	    $classes = self::classmap("map");
	    if(!$classes) {
	        return;
        }

		if(!array_key_exists($class."",$classes)) {
			return false;
        }

		if($extends) {
			if(!in_array($extends,$classes[$class]["p"]) && $extends!=$class) {
			    return false;
            }
		}

		return true;
	}

	
	/**
	 * Возвращает пкть к файлу класса
	 **/
	public function classPath($class) {
	
	    $map = self::classmap();
	    return $map["map"][$class]["f"];
	
	}
	
	/**
	 * Возвращает массив карты классов
	 **/
 	public static function classmap($key=null) {
 	
		// Загружаем карту класса по требованию
		if(self::$classmap === null) {
		    if(file_exists("../service/classmap.inc.php")) {
		        self::$classmap = include("../service/classmap.inc.php");
		    } else {
          		self::$classmap = array();
		    }
		}

		$ret = self::$classmap;

		if($key) {
		    $ret = $ret[$key];
		}

		return $ret;
	}
	
	public function setClassMap($classmap) {
		self::$classmap = $classmap;
	}
	
	public function includeClass($class) {
	
	    // Достаем путь к классу из карты классов
	    $path = $this->classPath($class);
	    
	    if($path) {
			include(mod::root()."/".$path);
		}
		
		// Если класс не нашелся в карте сайта, сканируем папку /mod/class
		// И подключаем все классыв ней рекурсивно
		else {
		
		    $class = preg_replace("/^mod_/","",$class);
			$a = explode("_",$class);
			$p1 = mod::root()."/mod/class/".implode("/",$a).".php";
			$p2 = mod::root()."/mod/class/".implode("/",$a)."/".$a[sizeof($a)-1].".php";
			if(file_exists($p1)) {
			    include_once($p1);
			} elseif(file_exists($p2)) {
			    include_once($p2);
			}
			
		}
	}

}
