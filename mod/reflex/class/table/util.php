<?

/**
 * Класс с утилитами для таблицы
 **/
class reflex_table_util extends mod_component {

	public static function collectNames() {
		$ret = array();
		foreach(mod::service("bundle")->all() as $mod) {
		    foreach(reflex_table::factoryModuleTables($mod->path()) as $table) {
		    	$ret[$table->name()] = $table->id();
		    }
		}
		file::mkdir("/reflex/system/");
		\infuso\util\util::save_for_inclusion("/reflex/system/names.inc.php",$ret);
	}
	
}
