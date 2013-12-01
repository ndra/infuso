<?

class mod_classmap_builder implements mod_handler {

	/**
	 * Парсит файл и возвращает массив с информацией о классе
	 * array(
	 *   "type" => "class" / "interface",
	 *   "class" => "className",
	 *   "abstract" => true / false
	 * )
	 **/
	public function getFileInfo($path) {
	
	    $ret = array(
	        "abstract" => false,
		);
	
	    $str = mod_file::get($path)->data();
	    $tokens = @token_get_all($str);
	    
	    $catchClassName = false;
	    foreach($tokens as $token) {
	    
	        switch($token[0]) {
	        
	            case T_CLASS:
	                $catchClassName = true;
	                $type = "class";
	                break;
	                
				case T_INTERFACE:
	                $catchClassName = true;
	                $type = "interface";
	                break;
	                
				case T_ABSTRACT:
				    $ret["abstract"] = true;
				    break;
	                
				// Игнорируем пробелы
				case T_WHITESPACE;
				    break;
	                
				default:
				    if($catchClassName) {
						$ret["type"] = $type;
						$ret["class"] = $token[1];
						return $ret;
						$catchClassName = false;
				    }
				    break;
	        }
	    }
	}

	/**
	 * Возвращает список классов модуля
	 * Сканирует все папки с классами, работает долго.
	 * Поэтому вызывается только при релинке для того чтобы построить карту классов
	 **/
	private static function classMap($secondScan) {

		$excludes = array();
		foreach(mod_file::get("/mod/exclude/")->dir() as $file) {
		    $excludes[] = $file->basename();
		}

		$ret = array();
		
		$bundles = mod::service("bundle")->all();
		
		foreach($bundles as $bundle) {
		    foreach($bundle->classPath()->search() as $file)
		        if($file->ext()=="php") {

		            $descr = array(
						"f" => $file->path(),
						"p" => array(),
					);

					$info = self::getFileInfo($file->path());
					$class = $info["class"];
					
		        	if(!$class)
		        	    continue;
		        	    
					if(array_key_exists($class,$ret) && !$secondScan) {
					    mod::msg("Duplicate file ".$file->path()." for class $class",1);
					}

		        	if($secondScan) {

		        	    // Предотвращаем фатальные ошибки при построении карты классов
		        	    // Если при инклуде файла произошла ошибка, то второй раз этот файл не подключится
		        	    // Пока не изменится его содержимое
					    $hash = md5($file->data());
					    if(in_array($hash,$excludes)) {
					        mod::msg("File ".$file->path()." disabled due fatal error on previous relink",1);
					        continue;
						}
						mod_file::mkdir("/mod/exclude/",1);
						mod_file::get("/mod/exclude/$hash.txt")->put($file->path());
						class_exists($class);
						mod_file::get("/mod/exclude/$hash.txt")->delete();

			        	// Отмечаем абстрактные классы
						$reflection = new ReflectionClass($class);
						if($reflection->isAbstract() || $reflection->isInterface())
							$descr["a"] = 1;

			        	// Расчитываем родителей
			        	$parent = $class;
			        	while($parent) {
			        	    $parent = get_parent_class($parent);
							if($parent)
								$descr["p"][] = $parent;
			        	}
		        	}

					$ret[$class] = $descr;

				}
			}
		return $ret;
	}

	public function sortbehaviours($a,$b) {
		$a = new $a;
		$b = new $b;
		return $a->behaviourPriority() - $b->behaviourPriority();
	}

	/**
	 * Функция возвращает поведения по умолчанию ввиде массива
	 * Этот массив будет использован при построении карты классов
	 **/
	public static function defaultBehaviours() {

		$ret = array();

		// Берем поведения по умолчанию (на основании mod_behaviour::addToClass)
		foreach(mod::service("classmap")->classes("mod_behaviour") as $class) {
		    $obj = new $class;
		    if($for = $obj->addToClass())
		        $ret[$for][] = $class;
		}

		// Сортируем поведения
		foreach($ret as $key=>$val) {
		    usort($ret[$key],array(self,"sortBehaviours"));
		}

		return $ret;
	}

	public static function handlers() {
		$handlers = array();
		foreach(mod::service("classmap")->classes() as $class=>$fuck) {
			$r = new ReflectionClass($class);
			if($r->implementsInterface("mod_handler")) {
				foreach($r->getMethods() as $method) {
				    if(preg_match("/^on_(.*)/",$method->getName(),$matches)) {
				        $handlers[$matches[1]][] = $class;
				    }
				}
			}
		}
		return $handlers;
	}
	
	/**
	 * Собирает описания служб
	 **/
	public static function services() {
		$services = array();
		foreach(mod::service("classmap")->classes() as $class=>$fuck) {
			$r = new ReflectionClass($class);
			if($r->isSubclassOf("mod_service") && !$r->isAbstract()) {
			    $item = new $class;
			    if($defaultService = $item->defaultService()) {
		        	$services[$defaultService] = $class;
		        }
			}
		}
		return $services;
	}
	
	public function sortRoutes($a,$b) {
	    $a = new $a();
	    $b = new $b();
	    return $b->priority() - $a->priority();
	}
	
	public static function getRoutes() {

		$ret = array();

		// Берем поведения по умолчанию (на основании mod_behaviour::addToClass)
		foreach(mod::service("classmap")->classes("mod_route") as $class) {
			$ret[] = $class;
		}
		
		// Сортируем поведения
	    usort($ret,array(self,"sortRoutes"));

		return $ret;
	}
	
	private static $building = false;

	/**
	 * Строит карту классов
	 **/
	public static function buildClassMap() {
	
	    if(self::$building) {
	        return array();
	    }
	
	    self::$building = true;
	
		$map = array();

		// расчитываем карту классов в два шага
		// На первом - просто собираем пути к файлам
		// На втором - родителей
		$map["map"] = self::classMap(false);
			    
		mod::service("classmap")->setClassMap($map);
		$map["map"] = self::classMap(true);
		mod::service("classmap")->setClassMap($map);
		

		
		$map["behaviours"] = self::defaultBehaviours();
		$map["handlers"] = self::handlers();
		$map["routes"] = self::getRoutes();
		$map["services"] = self::services();

		// Сохраняем карту классов в памяти, чтобы использовать ее уже в этом запуске скрипта
		mod::service("classmap")->setClassMap($map);

		mod_file::mkdir("/mod/service/");
		mod_file::get("/mod/service/classmap.inc.php")->put("<"."? return ".var_export($map,1)."; ?".">");
		mod_log::msg("Карта классов построена");
		
		self::$building = false;
	}

	public function on_mod_confSaved() {
		self::buildClassMap();
	}

}
