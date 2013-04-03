<?

class reflex_handler implements mod_handler {

	public function on_mod_init() {
	
	    user_operation::create("reflex:editLog","Редактирование лога")
			->appendTo("reflex:viewLog");
			
		user_operation::create("reflex:viewLog","Редактирование лога")
			->appendTo("admin");
	
	}

}
