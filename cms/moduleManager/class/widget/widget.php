<?

class moduleManager_widget extends admin_widget {

	public function exec() {

		$url = mod_action::get("moduleManager")->url();
		echo "<h2><a href='{$url}' >Управление модулями</a></h2>";

		$url = mod_action::get("moduleManager_newModule")->url();
		echo "<a href='{$url}' >Добавить модуль</a><br/>";

	}

	public function test() {
		return mod_superadmin::check();
	}

}
