<?

abstract class mod_route {

	public function priority() {
		return 0;
	}

	abstract public function forward($url);

	abstract public function backward($controller);

}
