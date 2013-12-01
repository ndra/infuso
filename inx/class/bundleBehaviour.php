<?

class inx_bundleBehaviour extends mod_behaviour {

	public function addToClass() {
	    return "bundle";
	}

	public function inxFolder() {
	    return $this->path()."/".$this->conf("inx","path");
	}

}
