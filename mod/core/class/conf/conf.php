<?

namespace infuso\core;

/**
 * Класс для загрузки конфигурации
 **/
class conf extends component {

	private static $path = "/conf/conf.xml";
	private static $generalConf = null;
	
	private static $alias = array(
		"reflex:mysql_host" => "mysql:host",
		"reflex:mysql_user" => "mysql:user",
		"reflex:mysql_password" => "mysql:password",
		"reflex:mysql_db" => "mysql:db",
		"reflex:mysql_table_prefix" => "mysql:table_prefix",
	);

	private static $cache = null;

	public function clearCache() {
		self::$cache = array();
	}

	public function path() {
		return self::$path;
	}
	
	/**
	 * Возвращает параметр из общей конфигурации components.yml
	 **/
	public function general() {
	
        // Если в буфере нет конфигурации - загружаем ее
        if(self::$generalConf===null) {

            $reader = new \mod_confLoader_yaml();
            $yml = file::get("/mod/conf/components.yml")->data();
            self::$generalConf = $reader->read($yml);

            if(!self::$generalConf) {
                self::$generalConf = array();
			}
        }

        // Если переданы параметры - извлекаем значения по ключам
        $ret = self::$generalConf;
        foreach(func_get_args() as $key) {
            $ret = $ret[$key];
        }

		return $ret;
	
	}

	/**
	 * Возвращает параметр конфигурации из /mod/conf/conf.xml
	 **/
	public function get($key) {

		if(!self::$cache) {

			if(!file::get(self::$path)->exists())
			    $cache = file::get("/mod/conf/mod.ini")->ini(1);
			else
				$cache = mod::loadXMLConf(self::$path);

			if(!$cache) $cache = array();

			self::$cache = array();
			foreach($cache as $kk=>$val) {
				if(is_array($val)) {
				    foreach($val as $k=>$v)
						self::$cache["$kk:$k"] = $v;
				} else {
					self::$cache[$kk] = $val;
				}
			}
		}

		$ret = self::$cache[$key];

		// Если параметр не найдем, пробуем найти алиас
		if(!$ret)
		    if($a = self::$alias[$key])
				$ret = self::get($a);

		return $ret;
	}
	
	public function name() {
	    return 123;
	}

}
