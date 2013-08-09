<?

class mod_route_service extends mod_service {

	public function defaultService() {
		return "route";
	}

	/**
	 * Очищает кэш url
	 **/
	public function clearCache() {
	    $ret = mod::service("cache")->clearByPrefix("action-url:");
	    if(!$ret) {
	        mod::service("cache")->clear();
	    }
	}

}
