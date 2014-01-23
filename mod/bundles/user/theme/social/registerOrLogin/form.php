<?

$name = user_social::active()->data("identity");
echo "Вы зашли как <b>$name</b><br/>";
echo "На нашем сайте еще нет учетных записей, связанных с этим социальным профилем.<br/><br/>";

$form = new form();
$form->radio()->name("action")->options(array(
    "register" => "Создать нового пользователя",
    "login" => "У меня уже есть учетная запись на ".mod_url::current()->server(),
))->value("register");
$form->submit("Далее");
$form->hidden("cmd","user_social_action:register");
$form->exec();