<?

$form = new form();
$form->textfield()->name("name")->label("Контактное лицо (ФИО)")->error("Укажите ваше имя")->min(5);
$form->email()->name("email")->label("Адрес электронной почты")->error("Это не электронная почта");
$form->textfield()->name("phone")->label("Телефон (10 цифр, например 792812345678)")->min(10);
/*$form->textfield()->name("street")->label("Улица")->min(3);
$form->textfield()->name("house")->label("Дом")->min(1);
$form->textfield()->name("building")->label("Строение");
$form->textfield()->name("flat")->label("Квартира / офис"); */
$form->textarea()->name("comments")->label("Комментарии к заказу");

$form->hidden("cmd","eshop:order:action:fillInForm");
$form->hidden("status","check");
$form->hidden("orderID",$p1->id());
$form->submit("Заказать доставку");
$form->exec();

echo "<br/><br/>";
echo "<a href='./' style='margin-left:220px;' >Вернуться с корзину</a>";