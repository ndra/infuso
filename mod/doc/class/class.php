<?

class doc_class extends mod_component {

	private $className = null;

	public function __construct($class=null) {
		$this->className = $class;
	}
	
	/**
	 * генерирует документацию класса
	 **/
	public function getDoc() {
	
	    $tmp = tmp::get("/doc/class", array(
	        "class" => $this->className,
		));
	
        tmp::pushConveyor();
        ob_start();
        $tmp->exec();
        $html = ob_get_clean();
        $conveyor = tmp::popConveyor();
        
        $content = array(
			"conveyor" => $conveyor->serialize(),
			"html" => $html,
		);
	
	    return array(
	        "content" => $content,
		);
	
	}

}
