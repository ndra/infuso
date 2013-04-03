<?

tmp::js("http://ulogin.ru/js/ulogin.js");
$url = "http://".mod_url::current()->server()."/user_social_action/back";
$rand= util::id();

<div>Войти через</div>
<div id="{$rand}" x-ulogin-params="display=small;optional=first_name,last_name,email,nickname,bdate,sex,phone,photo,photo_big,city,country;providers=vkontakte,odnoklassniki,mailru,facebook;hidden=other;redirect_uri={$url}"></div>
