<? 

$registerUrl = mod::action("user_action","register")->url();
$verificationUrl = mod::action("user_action","verificationResend")->url();
echo "Неправильная ссылка подтверждения почты.<br/><br/>";
echo "Попробуйте <a href='$verificationUrl' >запросить код подтверждения</a> повторно или <a href='{$url}' >зарегистрировтаься</a> еще раз.";