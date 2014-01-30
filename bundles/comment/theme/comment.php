<? 

<div class='w9nyivcoiv' >

    echo "Помогите другим пользователям нашего сайта: <span class='toggle' >оставьте отзыв</span>";
    
    <div class='form' >
    
        $form = new form();
        $form->select("mark")->label("Ваша оценка")->options(array(
            1 => "Ужасно!",
            2 => "Плохо",
            3 => "Нормально",
            4 => "Хорошо",
            5 => "Отлично!",
        ))->value(4);
        $form->textarea("text")->label("Ваш отзыв")->min(10)->error("Слишком короткий отзыв");
        $form->textarea("plus")->label("Достоинства");
        $form->textarea("minus")->label("Недостатки");
        $form->hidden("cmd","comment_controller:comment");
        $form->hidden("for",$for);
        $form->code("comment");
        $form->captcha("captcha");
        $form->submit("Отправить");
        $form->exec();
    
    </div>
    
</div>