<?

class reflex_editor_init implements mod_handler {

	public function on_mod_init() {
	
	    // Создаем роль «Контент-менеджер»
	    
	    $role = user_role::create("reflex:content-manager","Контент-менеджер");
	    $role->appendTo("admin");
	    user_operation::get("admin:showInterface")->appendTo("reflex:content-manager");
	    
	    // Добавляем операции
	
	    $op = user_operation::create("reflex:editItem");
	    $op->appendTo("reflex:content-manager");
	    
	    $op = user_operation::create("reflex:editConfValue","Редактирование значения настройки")
			->appendTo("admin");
			
		$op = user_operation::create("reflex:viewConf","Просмотр настроек")
			->appendTo("admin");
			
		// Добавляем вкладки в каталоге

        reflex_editor_rootTab::create(array(
            "title" => "Контент",
            "name" => "",
            "icon" => "/reflex/res/icons/48/content.png",
            "priority" => 1000,
		));
		
        reflex_editor_rootTab::create(array(
            "title" => "Системные",
            "name" => "system",
            "icon" => "/reflex/res/icons/48/system.png",
		));

	}

}
