<?

/**
 * Модель
 **/
abstract class mod_module extends mod_component {

	public function id() {
	    return get_class($this);
	}
	
	public function __toString() {
	    return $this->id();
	}
	
	public function title() {
	    return get_class($this);
	}
	
	public function publicFolders() {
	    return array();
	}
	
	public function updateUrl() {
	    return null;
	}

}
