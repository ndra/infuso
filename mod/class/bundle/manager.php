<?

/**
 * Служба управления бандлами
 **/
class mod_bundle_manager extends mod_service {

	public function defaultService() {
		return "bundle";
	}
	
	/**
	 * Возвращает список всех бандлов
	 **/
	public function all() {
	
	    $bundles = array();
	    $manager = $this;

	    $scan = function($path) use (&$scan, &$bundles, $manager) {

            $bundle = $manager->bundle($path);
			$leave = array();
			if($bundle->exists()) {
				$bundles[] = $bundle;
				$leave = $bundle->leave();
			}
	    
	        foreach(file::get($path)->dir()->folders() as $folder) {
				if(!in_array((string)$folder,$leave)) {
				    $scan($folder);
				}
	        }
	    
	    };
	    
		$scan("/");
		
		return $bundles;
	
	}
	
	public function bundle($bundle) {
	    return new mod_bundle($bundle);
	}

}
