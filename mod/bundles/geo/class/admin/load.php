<?

/**
 * Контроллер загрузки городов из базы
 **/
class geo_admin_load extends mod_controller {

public function indexTest() {
	return mod_superadmin::check();
}

public function postTest() {
	return mod_superadmin::check();
}

public function index() {
	admin::header("Загрузка городов");
	echo "<div style='padding:40px;' >";
	echo "<form method='post' >";
	echo "<input type='hidden' name='cmd' value='geo_admin_load::load' />";
	echo "<input type='submit' value='Загрузить города в базу' />";
	echo "</form>";
	echo "</div>";
	admin::footer();
}

public function post_load() {
	geo_admin_import::import();
}

}
