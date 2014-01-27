<?

namespace infuso\dao;

class connection {

	public function command() {
	    return new command($this);
	}

}
