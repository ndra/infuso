<?

class file_behaviour extends mod_behaviour {

	public function addToClass() {
	    return "mod_file_filesystem";
	}

	public function xls() {
	    return new file_xls($this->path());
	}

	public function time() {
		return util::date(@filemtime($this->native()));
	}

}
