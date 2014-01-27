<?

class seo_widget extends admin_widget {

	public function test() {
	    return user::active()->checkAccess("admin");
	}

	public function exec() {
		$url = mod_action::get("seo","list")->url();
		echo "<h2><a href='{$url}' >Отчет по продвижению</a></h2>";
	}

}
