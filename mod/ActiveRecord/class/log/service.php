<?

/**
 * Служба лога
 **/ 
class reflex_log_service extends mod_service {

	public function defaultService() {
	    return "log";
	}
	
	public function log($params) {
	    reflex::create("reflex_log",$params);
	}

}
