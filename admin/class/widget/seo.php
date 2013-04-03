<?

class admin_widget_seo extends admin_widget {

	public function test() {
	    return mod_superadmin::check();
	}

	/**
	 * Выполняет виджет
	 **/
	public function exec() {
        $url = mod_action::get("admin_seo")->url();
        echo "<h2><a href='{$url}' >SEO-настройки</a></h2>";
	}

	public function width() {
	    return 200;
	}

	public function inMenu() {
	    return true;
	}

	public function inStartPage() {
	    return false;
	}

}
