<?

namespace infuso\ActiveRecord;
use \infuso\core\mod;
use \infuso\core\file;

class init extends \mod_init {

	public function priority() {
	    return 9999;
	}

	public function init() {

	    mod::msg("<b>reflex</b>");

	    $v = mod::service("db")->query("select version()")->exec()->fetchScalar();
	    
	    if(floatval($v)<5) {
	        mod::msg("You need mysql version 5 or greater. You haver version $v",1);
	        return;
	    }
	    
	    mod::msg("mysql version {$v} ok");

		// Собираем имена таблиц
	    self::collectNames();

		// Проходимся по классам и создаем таблицы для них
		foreach(Record::classes() as $class) {
		    $table = Record::virtual($class)->table();
		    $table->migrateUp();
		}

	}
	
	/**
	 * Собирает имена таблиц в файл
	 * @todo со временем мы откажемся от хранения таблиц в отдельных файлах
	 **/
	public static function collectNames() {
	
		$ret = array();
		foreach(mod::service("bundle")->all() as $mod) {
		    foreach(table::factoryModuleTables($mod->path()) as $table) {
		    	$ret[$table->name()] = $table->id();
		    }
		}
		
		$dir = mod::app()->varPath()."/reflex";
		file::mkdir($dir);
		\infuso\util\util::save_for_inclusion("{$dir}/names.php",$ret);
	}

}
