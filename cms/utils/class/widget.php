<?

namespace infuso\cms\utils;

class widget extends \admin_widget {

	public function exec() {

		$url = \infuso\core\action::get(sql::inspector()->classname())->url();
		echo "<h2><a href='{$url}' >Консоль SQL</a></h2>";

	}

	public function test() {
		return \infuso\core\superadmin::check();
	}

}
