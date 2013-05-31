<?

class reflex_log_handler implements mod_handler {

	public function on_mod_cron() {

		// Удаляем старые записи из лога
		reflex_log::all()->leq("datetime",util::now()->shiftMonth(-6))->delete();
	
	}

}
