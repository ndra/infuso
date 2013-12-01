<?

class reflex_init extends mod_init {

	public function priority() {
	    return 9999;
	}

	public function init() {

	    mod::msg("<b>reflex</b>");

	    reflex_mysql::query("select version()");
	    
	    $v = reflex_mysql::scalar();
	    
	    if(floatval($v)<5) {
	        mod::msg("You need mysql version 5 or greater. You haver version $v",1);
	        return;
	    }

		// Собираем типы полей
	    mod_field::collect();

		// Собираем имена таблиц
	    reflex_table_util::collectNames();

		// Проходимся по классам и создаем таблицы для них
		foreach(reflex::classes() as $class) {
		    $table = reflex::virtual($class)->table();
		    $table->migrateUp();
		}
		
		self::buildEditorMap();
		reflex_editor_rootTab::removeAll();
		
	}
	
	public static function buildEditorMap() {
	    $map = array();
		foreach(mod::service("classmap")->classes("reflex_editor") as $class) {
		    $e = new $class;
		    $map[$e->itemClass()][] = $class;
		}
		util::save_for_inclusion("/reflex/system/editors.php",$map);
	}

}
