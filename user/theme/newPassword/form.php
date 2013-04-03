<?

$form = new form();
$form->password("password")->label("Новый пароль")->min(3);
$form->password("password2")->label("Повтор пароля")->match("password");
$form->hidden("id",$p1["id"]);
$form->hidden("code",$p1["code"]);
$form->hidden("cmd","user_action:changePassword");
$form->submit("Изменить");
$form->exec();

?>