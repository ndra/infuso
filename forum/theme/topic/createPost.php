<?php

$form = new form();

$form->attr("enctype", "multipart/form-data");

$form->code("forum_post_create_msnjn4jsbj");
$form->hidden()->name("cmd")->value("forum:post:create");
$form->hidden()->name("topic")->value($topic->id());

$form->textarea("message","Сообщение");

/*$captcha = mod::field("textfield");
$captcha->addBehaviour("form_fieldBehaviour");
$captcha->name("captcha");
$captcha->error("Число с картинки введено неверно. Попробуйте еще раз.");
$captcha->width(160);
$captcha->fn("form_kcaptcha::validate"); */

//$form->addField($captcha);

$form->submit("Отправить");

$form->exec();