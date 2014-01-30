<?

/**
 * Стандартная тема модуля geo
 **/
class geo_init implements mod_handler {

	public function on_mod_init() {
	
		// Операция редактирования города
	    user_operation::create("geo:editCity","geo: Редактирование города")
	        ->appendTo("admin");
	        
		// Операция редактирования рагиона
	    user_operation::create("geo:editRegion","geo: Редактирование региона")
	        ->appendTo("admin");
	        
		// Операция редактирования страны
	    user_operation::create("geo:editCountry","geo: Редактирование страны")
	        ->appendTo("admin");
	        
		// Операция редактирования города
	    user_operation::create("geo:importObjects","geo: Испорт объектов")
	        ->appendTo("admin");
	        
	}

}
