<?

class admin_widget_hello extends admin_widget {

	/**
	 * Выполняет виджет
	 **/
	public function exec() {
		tmp::exec("/admin/hello");
	}

	public function width() {
		return 600;
	}

	public function inMenu() {
		return false;
	}

	public function inStartPage() {
		return true;
	}

}
