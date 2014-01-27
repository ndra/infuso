<?

namespace infuso\core\bundle;

/**
 * Служба управления бандлами
 **/
class manager extends \infuso\core\service {

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
	    
	        foreach(\infuso\core\file::get($path)->dir()->folders() as $folder) {
				if(!in_array((string)$folder,$leave)) {
				    $scan($folder);
				}
	        }
	    
	    };
	    
		$scan("/");
		
		return $bundles;
	
	}
	
	public function bundle($bundle) {
	    return new bundle($bundle);
	}

}
