<?

namespace infuso\core;

class classmapService extends service {

	private static $extends = array();
	
	private static $classmap = null;

	public function defaultService() {
	    return "classmap";
	}
	
	public function classes($extends = null) {
	    return $this->getClassesExtends($extends);
	}
	
	public function map($extends = null) {
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
	
	private static $aliases = array(
	    "file" => "infuso\\core\\file",
	    "mod_file" => "infuso\\core\\file",
	    "mod_profiler" => "infuso\\core\\profiler",
	    "mod_url" => "infuso\\core\\url",
	    "mod" => "infuso\\core\\mod",
	    "mod_component" => "infuso\\core\\component",
	    "mod_controller" => "infuso\\core\\controller",
	    "mod_service" => "infuso\\core\\service",
	    "mod_superadmin" => "infuso\\core\\superadmin",
	    "mod_action" => "infuso\\core\\action",
	    "tmp" => "\\mod\\template\\tmp",
	    "tmp_widget" => "\\mod\\template\\widget",
	    "tmp_template" => "\\mod\\template\\template",
	);
	
	public function includeClass($class) {
	
	    $alias = self::$aliases[$class];
	    if($alias) {
	        self::includeClass($alias);
	        return;
	    }
	    
	    // Достаем путь к классу из карты классов
	    $path = $this->classPath($class);
	    
	    if($path) {
			include_once(mod::root()."/".$path);
		}
		
		// Если класс не нашелся в карте сайта, сканируем папку /mod/class
		// И подключаем все классыв ней рекурсивно
		else {
		    $class2 = strtr($class,array("\\" => "_"));
		    $class2 = preg_replace("/^(mod_)|(infuso_core_)/","",$class2);
			$a = explode("_",$class2);
			$p1 = mod::root()."/mod/class/".implode("/",$a).".php";
			$p2 = mod::root()."/mod/class/".implode("/",$a)."/".$a[sizeof($a)-1].".php";
			if(file_exists($p1)) {
			    include_once($p1);
			} elseif(file_exists($p2)) {
			    include_once($p2);
			}
			
		}
		
	    foreach(self::$aliases as $key => $val) {
	        if($val == $class) {
				class_alias($class,$key);
	        }
	    }
		
	}

}
