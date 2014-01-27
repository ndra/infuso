<?

class reflex_map extends mod_controller{

	public function indexTitle() { return "Переиндексировать"; }
	public function indexTest() { return mod_superadmin::check(); }
	public function indexFailed() { admin::fuckoff(); }
	public function index() {
	    admin::header("Переиндексировать каталог");
	    inx::add("inx.mod.reflex.map");
	    admin::footer();
	}

	private static $nn = 0;
	private static $deleted = 0;
	
	public static function postTest() {
		return mod_superadmin::check();
	}
	
	public static function post_step($p) {

	    $step = $p["step"];
	    $time = microtime(1);
	    $timeout = 3; // Максимальное время работы скрипта (в секундах)

	    if(!$p["step"]) {
	        mod_cmd::meta("total",self::total($mapper,$p));
	        self::beforeStart();
	    }

	    ob_start();
	    while((microtime(1)-$time<$timeout)) {

	        reflex::storeAll();

	        list($a,$b) = explode(":",$step);
	        if(!$a) $a = 0;
	        if(!$b) $b = 0;

	        $classes = self::classes();
	        if($p["className"]) $classes = array($p["className"]);
	        $class = $classes[$a];

	        if($classChanged) mod::msg("<b>".$class."</b>");
	        $classChanged = false;

	        if(!$class) {
	            $step = 0;
	            break;
	        }

	        $table = reflex_table::factoryTableForReflexClass($class)->name();
	        if($table) {
	            // Если у класса есть таблицы
	            $table = reflex_mysql::getPrefixedTableName($table);
	            $b = reflex_mysql::escape($b);
	            reflex_mysql::query("select `id` from `$table` where `id`>'$b' order by `id` limit 50 ",$b);
	            $ids = reflex_mysql::get_col();

	            foreach($ids as $id) {
	                self::processItem($class,$id);
	                if(ob_get_length()) return;
	                self::$nn++;

	            }
	            $lastID = end($ids);
	            if($lastID) $step = "$a:$lastID";
	            else {
	                $step = ($a+1).":0";
	                $classChanged = true;
	                self::logStats();
	            }
	        } else {
	            // Если у класса нет таблиц, просто переходим к следующему классу
	            $step = ($a+1).":0";
	        }
	    }

	    if($step) self::logStats();
	    else mod::msg("done");

	    ob_end_flush();
	    self::afterStep();

	    return $step;
	}

	public static function classes() {

	    $ret = unserialize(mod_cache::get("zfffff"));
	    if(!$ret) {

	        $ret = array();
	        foreach(reflex::classes() as $class)
	            if(reflex::get($class,0)->reflex_repairClass())
	                $ret[] = $class;
	        mod_cache::set("zfffff",serialize($ret));

	    }

	    return unserialize(mod_cache::get("zfffff"));
	}

	public static function logStats() {
	    $ret = "";
	    $ret.= "processed: ".self::$nn.", ";
	    if(reflex::deleted()) $ret.= "deleted: <span style='background:red;color:white;' >".reflex::deleted()."</span>, ";
	    if(reflex::created()) $ret.= "created: <span style='background:red;color:white;' >".reflex::created()."</span>, ";
	    if(reflex::stored()) $ret.= "stored: <span style='background:red;color:white;' >".reflex::stored()."</span>, ";
	    $ret.= "memory: ".(round(memory_get_usage()/10000)/100)." mb";
	    mod::msg($ret);
	    reflex::clearStatistics();
	}

	public static function processed() {
		return self::$nn;
	}

	public function total($mapper,$p) {

	    $n = mod_cache::get("xxf");
	    if(!$n) {

	        $classes = self::classes();
	        if($p["className"]) $classes = array($p["className"]);

	        $n = 0;
	        foreach($classes as $class) {
	            $table = reflex_table::factoryTableForReflexClass($class)->name();
	            if($table) {
	                $table = reflex_mysql::getPrefixedTableName($table);
	                reflex_mysql::query("select count(*) from `$table` ");
	                $n+= reflex_mysql::get_scalar();
	            }

	        }
	        mod_cache::set("xxf",$n);
	    }
	    return mod_cache::get("xxf");
	}

	public static function beforeStart() {
	    mod::msg("start");
	}

	public static function processItem($class,$id) {
	    $item = reflex::get($class,$id);
	    if($item->reflex_cleanup()) {
	        $item->delete();
	        self::$deleted++;
	    } else {
	        $item->callReflexTrigger("reflex_beforeStore");
	        $item->callReflexTrigger("reflex_repair");
	        $item->reflex_updateSearch();
	    }
	}

	public static final function afterStep() {
	    $ret = "";
	    foreach(mod_log::messages() as $msg) {
	        $ret.= $msg->text()."<br/>";
	    }
	    mod_cmd::meta("status",$ret);
	    mod_cmd::meta("processed",self::processed());
	}

	public static final function afterEnd() {
	    mod::msg("done");
	}

}
