<?

class doc_widget extends admin_widget {

	public function exec() {
		$url = mod_action::get("doc")->url();
		echo "<a href='{$url}' >Документация</a>";
	}

	public function test() {
		return mod_superadmin::check();
	}

}
