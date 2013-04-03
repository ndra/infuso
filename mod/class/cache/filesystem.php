<?

/**
 * Драйвер кэша файловой системы
 **/
class mod_cache_filesystem extends mod_cache_driver {

	/**
	 * Воозвращает файл переменнгой по ключу
	 **/
	private static function filename($key) {
	    $hash = md5($key);
	    $path = "/mod/cache/".substr($hash,0,2)."/$hash.txt";
	    return $path;
	}

	/**
	 * Возвращает значение переменной
	 **/
	public function get($key) {
		return file::get(self::filename($key))->data();
	}

	/**
	 * Устанавливает значение переменной
	 **/
	public function set($key,$val) {
		file::mkdir(file::get(self::filename($key))->up());
	    file::get(self::filename($key))->put($val);
	}

	/**
	 * Очищает кэш
	 * Удаляет папку /mod/cache/
	 **/
	public function clear() {
	    file::get("/mod/cache/")->delete(true);
	}

}
