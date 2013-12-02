<?

class inx_bundleBehaviour extends mod_behaviour {

	public function addToClass() {
	    return "mod_bundle";
	}

	public function inxPath() {

	    $inxPath = $this->conf("inx","path");

	    if($inxPath) {
	        return $this->path()."/".$inxPath;
	    }
	    
	    return null;

	}

}
