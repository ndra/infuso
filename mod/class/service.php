<?

/**
 * Базовый класс для служб
 **/
class mod_service extends mod_controller {

	public function defaultService() {
	    return false;
	}

	public static function get($name) {
	
	    $class = mod_conf::general("services",$name,"class");
	    
	    if(!$class) {
		    $services = mod::classmap("services");
		    $class = $services[$name];
	    }
	    
	    if(!$class) {
	        throw new Exception("Service [$name] not found");
	    }
	    
	    return new $class;
	
	}

}
