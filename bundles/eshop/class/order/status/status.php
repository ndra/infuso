<?

/**
 * Модель статуса заказа
 **/
class eshop_order_status extends mod_component {

	/**
	 * Возвращает массив с экземплярами все классов заказов
	 **/
	public static function all() {
	
		$ret = array();
		foreach(mod::service("classmap")->map("eshop_order_status") as $class) {
		    $status = new $class;
		    if($status->exists()) {
		        $ret[] = $status;
		    }
		}

		usort($ret,array("self","sortStatuses"));

		return $ret;
	}

	private static function sortStatuses($a,$b) {
		return $b->priority() - $a->priority();
	}

	public function _priority() {
		return 0;
	}

	/**
	 * Возвращает объект класса заказа по id (имени класса)
	 **/
	public static function get($class) {
		if(mod::service("classmap")->testClass($class,"eshop_order_status")) {
		    return new $class;
		}
		return new eshop_order_status_none();
	}


	/**
	 * @return Возвращает название статуса
	 **/
	public function _title() {
	}

	public final function name() {
		return $this->title();
	}

	/**
	 * @return Возвращает описание статуса
	 **/
	public function _descr() {
	}

	/**
	 * Существует ли данный статус заказа
	 **/
	public function _exists() {
		return true;
	}

	public function _beforeSet() {
		return true;
	}

	public function _afterSet() {
		return true;
	}

}
