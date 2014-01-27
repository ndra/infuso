<?

/**
 * Контроллер отчета крона
 **/
class mod_cron_log extends mod_controller {

	public function indexFailed() {
	    admin::fuckoff();
	}

	public function indexTest() {
	    return mod_superadmin::check();
	}
	
	public function index() {
	    tmp::exec("/mod/cron");
	}

}
