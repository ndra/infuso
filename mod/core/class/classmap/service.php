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
		
		    $extends = strtolower($extends);

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
	
	private static function prepareClass($class) {
	    $class = strtolower($class);
	    $class = trim($class,"\\");
	    return $class;
	}
	
	public static function testClass($class,$extends=null) {
	
	    $class = self::prepareClass($class);
	    $extends = self::prepareClass($extends);
	    
		if($class=="mod" && $extends=="mod_controller") {
		    return true;
        }

	    $classes = self::classmap("map");
	    if(!$classes) {
	        return;
        }
        
	    if($class=="infuso\\board\\collectionbehaviour") {
	        //var_export(array_key_exists($class."",$classes));
	        echo "<pre>";
	        var_export($classes);
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
	 * Возвращает путь к файлу класса
	 * Пытается найти информацию о пути в карте классов
	 * Если такой инфомрации не на найдено и класс загружен, использует Reflection
	 **/
	public function classPath($class) {
	
	    $class = strtolower($class);
	
	    // Пробуем использовать карту сайта
	    $map = self::classmap();
	    $path = $map["map"][$class]["f"];
	    
		// Если не найдено, используем Reflection
	    if(!$path && class_exists($class,false)) {
	        $reflector = new \ReflectionClass($class);
         	$abspath = $reflector->getFileName();
         	$root = rtrim(file::get("/")->native(),"/");
         	$path = substr($abspath,strlen($root));
         	$path = strtr($path,'\\','/');
	    }
	    
	    return $path;
	}
	
	/**
	 * Возвращает массив карты классов
	 * @todo не делать лишних проверок существования /var/classmap.php
	 **/
 	public static function classmap($key=null) {
 	
		// Загружаем карту класса по требованию
		if(self::$classmap === null) {
		    if(file::get(self::classMapPath())->exists()) {
		        self::$classmap = include(file::get(self::classMapPath())->native());
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
	
	public static function classMapPath() {
	    return mod::app()->varPath()."/classmap.php";
	}
	
	public function setClassMap($classmap) {
		self::$classmap = $classmap;
	}
	
	/**
	 * Сохраняет карту сайта на диск
	 **/
	public function storeClassMap($map) {
	    $this->setClassMap($map);
	    file::get(self::classMapPath())->put("<"."? return ".var_export($map,1)."; ?".">");
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
	    "mod_field" => "infuso\\core\\field",
	    "mod_behaviour" => "infuso\\core\\behaviour",
	    "tmp" => "\\mod\\template\\tmp",
	    "tmp_widget" => "\\mod\\template\\widget",
	    "tmp_template" => "\\mod\\template\\template",
	    "util" => "\\infuso\\util\\util",
	    "reflex" => "\\infuso\\activerecord\\record",
	);
	
	/**
	 * Возвращает объект бандла для данного класса
	 **/
	public function getClassBundle($class) {
	
	    $class = strtolower($class);
	    $path = file::get($this->classPath($class));
	    
	    while($path->name() != "class" && $path != "/") {
	        $path = $path->up();
	    }
	    
	 	return new \infuso\core\bundle\bundle($path->up());
	}
	
	/**
	 * Папка с системными классами, которую надо сканирвоать в случае ошибки
	 **/
	public function scanFolder() {
	    return $this->getClassBundle(get_class())->classPath();
	}
	
	public function includeClass($class) {
	
	    $class = strtolower($class);
	
		// Если класс является алиасом, то подключаем сам класс, а не алиас
	    $alias = self::$aliases[$class];
	    if($alias) {
	        self::includeClass($alias);
	        return;
	    }
	    
	    // Достаем путь к классу из карты классов
	    $path = $this->classPath($class);
	    
		// Если класс найден в карте сайта, подключаем
	    if($path) {
			include_once(mod::root()."/".$path);
		}
		// Если класс не нашелся в карте сайта, пробуем подключить класс напрямую
		else {
		
		    $class2 = strtr($class,array("\\" => "_"));
		    $class2 = preg_replace("/^(mod_)|(infuso_core_)/","",$class2);
			$a = explode("_",$class2);
			$p1 = mod::root()."/".$this->scanFolder()."/".implode("/",$a).".php";
			$p2 = mod::root()."/".$this->scanFolder()."/".implode("/",$a)."/".$a[sizeof($a)-1].".php";
			
			if(file_exists($p1)) {
			    include_once($p1);
			} elseif(file_exists($p2)) {
			    include_once($p2);
			}
			
		}
		
		// регистрируем алиас файла
	    foreach(self::$aliases as $key => $val) {
	        if($val == $class) {
				class_alias($class,$key);
	        }
	    }
		
	}

}
