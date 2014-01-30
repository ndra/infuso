<?php

$form = new form();

$form->attr("enctype", "multipart/form-data");

$form->code("forum_post_create_msnjn4jsbj");
$form->hidden()->name("cmd")->value("forum:post:create");
$form->hidden()->name("topic")->value($topic->id());

$form->template("files");

$form->textarea("message","Сообщение")->min(5);

$form->submit("Отправить");

$form->exec();