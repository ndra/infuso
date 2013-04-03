<?

class doc_class extends mod_controller {

	private $className = null;

	public function get($class) {
		return new self($class);
	}

	public function __construct($class=null) {
		$this->className = $class;
	}

	public function url() {
		return mod_action::get("doc","class",array("class"=>$this->className))->url();
	}

}
