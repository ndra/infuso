<?

$user = user::active();

$form = new form();

$form->textfield("firstName")->label("Имя")->value($user->firstName());
$form->textfield("lastName")->label("Фамилия")->value($user->lastName());
$form->email("email")->fn("user_action:registerCheckEmail")->label("Электронная почта")->value($user->email());

$form->password("password")->label("Пароль")->min(3);
$form->password("password2")->label("Повтор пароля")->match("password");
$form->hidden("cmd","user_action:register");
$form->submit("Зарегистрировать");
$form->code("user:register");
$form->exec();