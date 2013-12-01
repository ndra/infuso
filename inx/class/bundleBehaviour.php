<?

class inx_bundleBehaviour extends mod_behaviour {

	public function addToClass() {
	    return "mod_bundle";
	}

	public function inxPath() {
	    return $this->path()."/".$this->conf("inx","path");
	}

}
