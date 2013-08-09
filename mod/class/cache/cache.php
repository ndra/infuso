<?

class mod_cache extends mod_service {

	private static $driver = null;
	private static $read = 0;
	private static $write = 0;
	private static $memoryCache = array();
	
	public function defaultService() {
	    return "cache";
	}
	
	/**
	 * Возвращает драйвер кэширущей системы
	 **/
	private function driver() {

	    if(!self::$driver) {
	        switch(mod_conf::get("mod:cacheDriver")) {
	            default:
	            case "filesystem":
	                self::$driver = new mod_cache_filesystem();
	                break;
	            case "xcache":
	                self::$driver = new mod_cache_xcache();
	                break;
	        }
	    }
	    return self::$driver;
	}

	/**
	 * @return Возвращает количество операций считывания
	 **/
	public static function read() {
	    return self::$read;
	}

	/**
	 * @return Возвращает количество операций записи
	 **/
	public static function write() {
	    return self::$write;
	}

	/**
	 * @return mixed Возвращает значение переменной из кэша
	 **/
	public static function get($key) {

	    mod_profiler::beginOperation("cache","read",$key);

	    self::$read++;
	    if(!array_key_exists($key,self::$memoryCache)) {
	        self::$memoryCache[$key] = self::driver()->get($key);
		}

	    mod_profiler::endOperation();

	    return self::$memoryCache[$key];
	    
	}

	/**
	 * Записывает переменную в кэш
	 **/
	public static function set($key,$val) {
	    mod_profiler::beginOperation("cache","write",$key);
	    self::$write++;
	    self::driver()->set($key,$val);
	    self::$memoryCache[$key] = $val;
	    mod_profiler::endOperation();
	}

	public static function clear() {
	    self::driver()->clear();
	}
	
	public static function clearByPrefix($prefix) {
	    self::driver()->clearByPrefix($prefix);
	}

}
