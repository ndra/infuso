<? class reflex_mysql_cleanup extends mod_controller {

public static function indexTest() {
	return mod_superadmin::check();
}

public static function postTest() {
	return mod_superadmin::check();
}

public static function indexTitle() {
	return "Таблицы MySQL";
}

public static function index($preview=null) {
    admin::header("Таблицы MySQL");
    inx::add(array(type=>"inx.mod.reflex.admin"));
    admin::footer();
}
public static function indexFailed() { admin::fuckoff(); }

// -----------------------------------------------------------------------------

public static function tables() {
	$ret = array();
	reflex_mysql::query("show tables");
	foreach(reflex_mysql::get_col() as $fullname) {
	    $prefix = reflex_mysql::prefix();
	    $name = preg_replace("/^$prefix/","",$fullname);
	    $table = reflex_table::factoryByName($name);
	    $exists = $table->exists();
	    
	    if($exists) {
		    reflex_mysql::query("select count(*) from `{$table->prefixedName()}`");
		    $rows = reflex_mysql::get_scalar();
	    } else {
	        $rows = 0;
	    }
	    
	    $ret[] = array(
	        "name" => $fullname,
	        "exists" => $exists ? "ok" : null,
			"rows" => $rows
		);
	}
	return $ret;
}

public static function post_getTables() {
	$ret = self::tables();
	mod_cmd::meta("cols",array(
	    "exists" => array("type"=>"image"),
	    "name" => array("width"=>300,"title"=>"Таблица"),
	    "rows" => array("width"=>50,"title"=>"Строк")
	));
	return $ret;
}

public static function post_removeUnnecessary() {
	foreach(self::tables() as $table)
	    if(!$table["exists"])
	        reflex_mysql::query("drop table `{$table[name]}` ");
}

// -----------------------------------------------------------------------------

} ?>
