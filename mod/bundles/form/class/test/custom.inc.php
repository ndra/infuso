<?

class form_test_custom extends mod_controller {

	public static function indexTest() {
		return true;
	}
	
	public static function index() {
		tmp::header();
		echo "<form id='xxx' style='padding:50px;' >";

		echo "<div>";
		echo "<input name='field1' />";
		echo "<div class='error-field1' ></div>";
		echo "</div>";

		echo "<div>";
		echo "<input name='field2' style='font-size:2em;border-radius:5px;' />";
		echo "<div class='error-field2' ></div>";
		echo "</div>";

		echo "<input type='submit' />";

		echo "</form>";
		$form = new form();
		$form->textfield("field1")->min(3);
		$form->textfield("field2")->min(3);
		$form->bind("#xxx");
	
		tmp::footer();
	}

}
