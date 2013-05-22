<?php

$form = new form();

$form->attr("enctype", "multipart/form-data");

$form->code("forum_post_edit_sjxnr6l0j0");
$form->hidden()->name("cmd")->value("forum_post:edit");
$form->hidden()->name("post")->value($post->id());

$form->template("remainFiles", array("attachments" => $post->attachments()));

$form->template("addFiles");

$form->textarea("message","Сообщение")->value($post->data("message"))->min(5);

$form->submit("Сохранить и вернуться");

$form->exec();