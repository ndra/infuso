<?

/**
 * Абстрактный класс контент-процессорв
 **/
abstract class reflex_content_processor extends mod_service {

	/**
	 * Возврашает процессор контента по имени класса
	 **/
	public static function get($class,$conf=array()) {
	
	    if(!mod::service("classmap")->testClass($class,"reflex_content_processor")) {
	        throw new Exception("Class $class is not content processor;");
	    }
	
	    $processor = new $class();
		return $processor;
	}
	
	/**
	 * Возвращает контент процессор по умолчанию
	 **/
	public function getDefault() {
	    return mod::service("contentProcessor");
	}
	
	/**
	 * Обработчик контента
	 * Определите эту функцию в конечном классе
	 **/
	abstract function process($input);

}
