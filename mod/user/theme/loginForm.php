<?

tmp::jq();
tmp::nocache();

$user = user::active();
if($user->exists()) {

    <span class='ufaa7h-username' >Вы — {$user->title()}</span>
    mod::coreJS();
    <span class='ufaa7h-exit' onclick='mod.cmd({cmd:"user_action:logout"},function() {window.location.href="/"})' >Выйти</span>
    <div style='font-size:.8em;' >
    
    $url = mod_action::get("user_action","update")->url();
        <a href='$url' >Личный кабинет</a>
    </div>
      
} else {

    <form class='arwfr2nnm9' method='post' >
    
        // Логин
        <div class='email' >
            <div class='placeholder' >e-mail</div>
            <input class='ufaa7h0-field' name='login' >
        </div>
    
        // Пароль
        <div class='password' >
            <div class='placeholder' >Пароль</div>
            <input type='password' class='ufaa7h0-field' name='password'>
        </div>
        
        <div>
            <input type='checkbox' name='keep' id='remember-me' style='vertical-align:middle;' checked='checked' />
            <label for='remember-me' style='vertical-align:middle;' >Запомнить меня</label>
        </div>
        
        <input type='submit' value='Войти' />
        
        <input type='hidden' name='cmd' value='user_action:login' />
    </form>
    
    <div style='font-size:.8em;' >    
        $url = mod_action::get("user_action","register")->url();
        echo "<a href='$url'>Регистрация</a> ";
        $url = mod_action::get("user_action","lost")->url();
        echo "<a href='$url'>Забыли пароль</a>";
    </div>
    
    /*if(mod::conf("user:social")) {
        <div style='padding-top:10px;' >
            tmp::exec("user:social.login");
        </div>
    } */
    
}