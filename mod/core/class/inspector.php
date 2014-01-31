<?

namespace infuso\core;

/**
 * Класс-инспектор (похож на ReflectionClass)
 **/
class inspector {

	private $className;

	public function __construct($className) {
	    $this->className = $className;
	}
	
	public function bundle() {
	    return mod::service("classmap")->getClassBundle($this->className);
	}
	
	public function className() {
	    return $this->className;
	}

}
