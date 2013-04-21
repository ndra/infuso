<? class update_widget extends admin_widget {

public function exec() {
	$url = mod_action::get("update")->url();
	echo "<h2><a href='{$url}' >Push :)</a></h2>";
}

public function test() {
	return mod_superadmin::check();
}

}
