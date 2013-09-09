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
        return $_SERVER["DOCUMENT_ROOT"]."/";
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
    public function set($key, $val, $ttl = null) {
        xcache_set(self::prefix().$key,$val,$ttl);
    }

    /**
     * Очищает кэш
     **/
    public function clear() {
        xcache_unset_by_prefix("");
    }
    
    /**
     * Очищает кэш
     **/
    public function clearByPrefix($prefix) {
        xcache_unset_by_prefix(self::prefix().$prefix);
        return true;
    }

}
