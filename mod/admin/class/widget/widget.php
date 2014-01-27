<?

abstract class admin_widget extends mod_component {

	/**
	 * Выполняет виджет
	 **/
	abstract public function exec();

	/**
	 * @return Выводить ли виджет в меню
	 **/
	public function inMenu() {
		return true;
	}

	/**
	 * @return Выводить ли виджет на главной странице
	 **/
	public function inStartPage() {
		return false;
	}

	/**
	 * @return Возвращает ширину виджета в пикселях
	 **/
	public function width() {
		return 200;
	}

	/**
	 * @return Можно ли выводить этот виджет
	 **/
	public function test() {
	    return user::active()->checkAccess("admin:showInterface");
	}

	/**
	 * Возвращает список всех виджетов
	 **/
	public function all() {
		$ret = array();
		foreach(mod::service("classmap")->getClassesExtends("admin_widget") as $class) {
		    $widget = new $class;
		    if($widget->test())
		        $ret[] = $widget;
		}
		return $ret;
	}

}
