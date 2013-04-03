<?

$user = user::active();
$form = new form();
$form->email("email")->fn("user_action:registerCheckEmail")->label("Электронная почта")->value($user->email());
$form->textfield("firstName")->label("Имя")->value($user->firstName());
$form->textfield("lastName")->label("Фамилия")->value($user->lastName());
$form->password("password")->label("Новый пароль")->min();
$form->password("password2")->label("Повтор пароля")->match("password");
$form->hidden("cmd","user:action:update");
$form->code("user:update");
$form->submit("Сохранить");
$form->exec();