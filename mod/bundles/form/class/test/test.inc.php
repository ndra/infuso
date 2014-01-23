<?

class form_test extends mod_controller {

	public static function indexTest() {
		return mod_superadmin::check();
	}

	public static function index() {
	    tmp::header();
	    echo "<style>body{background:#ededed;}</style>";
	    echo "<div style='padding:50px;' >";
	    self::form()->exec();
	    echo "</div>";
	    tmp::footer();
	}

	public function validate($value,$data,$field) {
	    if($value<10)
	        return "Вы ввели $value. Введите число больше 10.";
	    return true;
	}

	public function form() {

	    $form = new form();

	    $form->textfield()->label("Валидация функцией")->name("ff")->fn("form_test::validate");

	    $form->html("<div style='padding:20px;margin:0px 0px 10px 200px;border:2px dashed gray;width:50%;' >Custom layout and html<br/><br/>");
	    for($i=0;$i<10;$i++)
	        $form->textfield("xx87h-$i",$i)->layout("none")->min(3)->width(50);
	    $form->html("</div>");

	    $f = $form->email()->label("Почта")->name("email")->value("Почта почта");

	    $form->textfield()->regex("/\d{5}/")->label("Тест режекса")->name("reg");
	    $form->heading("А вот и заголовок");
	    $form->password()->name("p1")->label("Пароль")->min(2)->error("Введите пароль")->value(123);
	    $form->password()->name("p2")->label("Подтверждение пароля")->match("p1")->error("Пароль и подтверждение пароля не совпадают");

	    $form->textfield()->label("Ололо!")->name("ololo")->min(2)->max(5)->error("Ололо должно быть от 2 до 5 символов");
	    $form->textarea()->label("А вот пример текстового поля")->name("ta")->min(10)->value("oops'<b></b>");
	    $form->textfield()->value("Превед")->name("pw");
	    $form->textfield()->name("fuck");
	    $form->hidden()->name("cmd")->value("form:test:a");
	    $form->checkbox()->name(666)->label("Чекбокс")->value(1);
	    $form->checkbox()->name(667)->label("Чекбокс2")->value(0);
	    $form->select()->name(88)->options(array(12,34,456));
	    //$form->radio()->name("xx-radio-xx")->options(array(1=>12,2=>34,3=>456))->value(1);//->fill();
	    $form->file()->name("file");
	    $form->captcha()->name("captcha")->label("Вырви-глаз капча");

	    $form->submit("Пыщ!");
	    return $form;
	}

	public static function postTest() { return true; }
	public static function post_a($r) {
	    $form = self::form()->setData($r);
	    echo nl2br($form->text());
	}

}
