<?

admin::header("Запрос MySQL");

<form style='padding:40px;' method='post' >

    tmp::exec("recent");

    <textarea style='width:100%;' name='q' >
        echo htmlspecialchars($_POST["q"],ENT_QUOTES);
    </textarea>
    <input type='submit' value='Отправить' >
</form>

<div style='padding:40px;' >

    tmp::exec("result");

</div>

admin::footer();