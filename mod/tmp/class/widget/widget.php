<?

namespace mod\template;
use infuso\core;

/**
 * Базовый абстрактный класс для всех виджетов
 **/
abstract class widget extends generic {

	/**
	 * @return Возвращает объект виджета
	 */
	public final function get($class) {
	
	    if(is_object($class)) {
	    
	        if(!mod::testClass(get_class($class),"tmp_widget")) {
	            throw new Exception(get_class($class)." is not a widget");
	        }
	        
	        return $class;
	    
	    }
	
	    if(!\mod::service("classmap")->testClass($class,"mod\\template\\widget")) {
	        throw new \Exception("$class is not a widget");
	    }
	
	    return new $class;
	}

	/**
	 * @return Название виджета
	 * Вы должны определить этот метод
	 **/
	abstract function name();

	/**
	 * Вызов виджета
	 * Вы должны определить этот метод
	 **/
	abstract public function execWidget();

	/**
	 * @return array Возвращает массив с виджетами
	 **/
	public final function all() {
	    $ret = array();
	    foreach(mod::classes("tmp_widget") as $class) {
	        $ret[] = new $class;
	    }
	    return $ret;
	}

	private static $stack = array();

	public final function begin($params=array()) {
		foreach($params as $key=>$val)
			$this->param($key,$val);
		self::$stack[] = $this;
		ob_start();

		return $this;
	}

	public final function end() {
		$widget = array_pop(self::$stack);
		$content = ob_get_clean();
		$this->param("content",$content);
		$widget->exec($widget->param());
	}

	public final function exec() {
		$this->execWidget($this->param());
	}

}
