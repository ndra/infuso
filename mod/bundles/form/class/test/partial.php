<?

class form_test_partial extends mod_controller {

	public static function indexTest() {
		return mod_superadmin::check();
	}

	public static function index() {
		tmp::header();
		echo "<div style='padding:40px;' >";
		$form = new form();
		$form->textfield("field1")->label("field1")->min(3);
		//$form->textfield("field2")->label("field2")->min(3)->validateIf("field1",array(123,567));
		$form->submit("ололо!");
		//$form->select("ололо!");
		$form->exec();
		echo "</div>";
		tmp::footer();
	}

}
