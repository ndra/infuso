<?

/**
 * Класс-генератор документации
 **/
class doc_collect extends mod_controller {

	public function indexTest() {
	    return true;
	}
	
	public function index() {
	    self::collect();
	}

	public function collect() {
	
		set_time_limit(0);
	
	    doc_page::all()->delete();
	    
		foreach(mod::classes() as $class => $extends) {
		
		    $class = new doc_class($class);
			$doc = $class->getDoc();
		
		    reflex::create("doc_page", array(
		        "title" => $class,
		        "content" => json_encode($doc["content"]),
			));
		}
	
	}
	
}
