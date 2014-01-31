<?

namespace infuso\ActiveRecord;

class handler implements \mod_handler {

	public function on_mod_init() {
	
	    \user_operation::create("reflex:editLog","Редактирование лога")
			->appendTo("reflex:viewLog");
			
		\user_operation::create("reflex:viewLog","Редактирование лога")
			->appendTo("admin");

        \reflex_task::add(array(
            "class" => "reflex_handler",
            "method" => "cleanup",
            "crontab" => "0 0 * * *",
        ));
	
	}

    public static function cleanup() {

		// Удаляем старые записи из лога (7 месцев)
		\reflex_log::all()->leq("datetime",util::now()->shiftMonth(-6))->delete();

		// Удаляем старые руты из каталога (7 дней)
		\reflex_editor_root::all()->leq("created",util::now()->shiftDay(-7))->delete();

		// Удаляем старые конструкторы (7 дней)
		\reflex_editor_constructor::all()->leq("created",util::now()->shiftDay(-7))->delete();
		
		// Удаляем старые задачи (60 дней)
		\reflex_task::all()->eq("completed",1)->leq("created",util::now()->shiftDay(-60))->delete();

    }
    
    public function on_mod_appShutdown() {
        Record::storeAll();
    }

}
