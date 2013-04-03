<?

echo "Для повторной отправки ссылки на подтверждение электронной почты введите ее в форму ниже и нажмите «Отправить».<br/>";
echo "На указанную электронную почту будет выслано письмо с инструкциями.";
echo "<br/><br/>";

$form = new form();
$form->email("email")->label("Адрес вашей электронной почты")->value($_REQUEST["email"]);
$form->hidden("cmd","user:action:resendVerification");
$form->submit("Продолжть");
$form->exec();