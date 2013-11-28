<? 

admin::header("Настройка компонентов");

<form class='gfpkv491' method='post' >

    tmp::exec("../menu");

    <textarea name='conf' >
        echo util::str($conf)->esc();
    </textarea>
    
    <input type='submit' value='Сохранить' />
    
    <input type='hidden' name='cmd' value='mod_conf_controller/saveComponentsConf' />
    
</form>

admin::footer();