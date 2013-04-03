<?

/**
 * Драйвер кэша для xcache
 **/
class mod_cache_xcache extends mod_cache_driver {

	private static $prefix = null;

	/**
	 * Возвращает префикс ключа
	 * Этот префикс меняется каждый раз при вызове метода clear(), тем самым эмулируется
	 * очистка кэша
	 **/
	public function prefix() {
	    /*if(!self::$prefix) {
	        self::$prefix = xcache_get("ok1bdpvrqw92d8lbx8u0");
	        if(!self::$prefix) {
	            xcache_set("ok1bdpvrqw92d8lbx8u0",util::id(5));
	            self::$prefix = xcache_get("ok1bdpvrqw92d8lbx8u0");
	        }
	    }
	    return self::$prefix; */
	    
	    return $_SERVER["DOCUMENT_ROOT"];
	}

	/**
	 * Возвращает значение переменной
	 **/
	public function get($key) {
	    return xcache_get(self::prefix().$key);
	}

	/**
	 * Устанавливает значение переменной
	 **/
	public function set($key,$val) {
	    xcache_set(self::prefix().$key,$val);
	}

	/**
	 * Очищает кэш
	 **/
	public function clear() {
	    //xcache_set("ok1bdpvrqw92d8lbx8u0",util::id(5));
	    
	    xcache_unset_by_prefix("");
	}

}
