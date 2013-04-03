<?

/**
 * Класс с утилитами для таблицы
 **/
class reflex_table_util extends mod_component {

	public static function collectNames() {
		$ret = array();
		foreach(mod::all() as $mod)
		    foreach(reflex_table::factoryModuleTables($mod) as $table)
		    	$ret[$table->name()] = $table->id();
		file::mkdir("/reflex/system/");
		util::save_for_inclusion("/reflex/system/names.inc.php",$ret);
	}
	
}
