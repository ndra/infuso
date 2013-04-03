<?

class form_test_layout extends mod_controller {

	public static function indexTest() {
		return mod_superadmin::check();
	}

	public static function index() {
	    tmp::header();
	    echo "<div style='padding:50px;' >";
	    
	    $form = new form();
	    $form->textfield()->label(123)->value(121212)->min(4);
	    $form->radio("sss","sdsd")->options("sdfsdf,sdfwerr,werew")->value(2);
	    $form->submit("Отправить");
	    $form->heading(12122);
	    $form->html(dddddddddddddd2)->label(1212);
	    $form->widget("site_widget");
	    $form->purehtml(dddddddddddddd2);
	    $form->exec();
	    
	    echo "</div>";
	    tmp::footer();
	}

}
