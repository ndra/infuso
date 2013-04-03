<?

class mod_cache extends mod_controller {

	public static function indexTitle() {
		return "Кэш";
	}

	public static function indexTest() {
		return mod_superadmin::check();
	}

	public static function postTest() {
		return mod_superadmin::check();
	}

	public static function index() {
	    admin::header("Кэш");
	    $files = file::get("/mod/cache/")->search();
	    echo "<div style='padding:40px;' >";
	    echo "Cached {$files->length()} items (".round($files->size()/1000000)." Mb)";
	    echo "<br/><br/>";
	    inx::add(array("type"=>"inx.button","text"=>"Очистить кэш","onclick"=>"this.call({cmd:'mod:cache:clear'},function() {window.location.reload()})"));
	    echo "</div>";
	    admin::footer();
	}

	public static function post_clear() {
	    self::clear();
	}

	private static $driver = null;

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

	private static $read = 0;
	private static $write = 0;

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

	private static $memoryCache = array();

	/**
	 * @return mixed Возвращает значение переменной из кэша
	 **/
	public static function get($key) {

	    mod_profiler::beginOperation("cache","read",$key);

	    self::$read++;
	    if(!array_key_exists($key,self::$memoryCache))
	        self::$memoryCache[$key] = self::driver()->get($key);

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

}
