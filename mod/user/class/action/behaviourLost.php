<?

/**
 * Поведение, реализующие механизм восстановления пароля
 **/
class user_action_behaviourLost extends mod_behaviour {

    public function behaviourPriority() {
        return -1;
    }

    public function addToClass() {
        return "user_action";
    }

    /**
     * Экшн страницы восстановления пароля
     **/
    public function index_lost() {
        tmp::noindex();
        tmp::exec("user:lost");
    }
    
    /**
     * Экшн страницы, информирующей о том, что данные восстановления высланы
     **/
    public function index_lostSend() {
        tmp::noindex();
        tmp::exec("user:lostSend");
    }
    
    /**
     * Экшн страницы ввода нового пароля
     **/
    public function index_newPassword($p) {
        tmp::exec("user:newPassword",$p);
    }

    /**
     * Экшн страницы с инфомрацией о том что пароль изменен
     **/
    public function index_passwordChanged($p) {
        tmp::noindex();
        tmp::exec("user:passwordChanged",$p);
    }

    /**
     * Команда отправки на email информации для восстановления пароля
     **/
    public function post_lost($p) {
        $user = user::byEmail($p["email"]);
        
        $url = self::changePasswordURL($user);
        
        $msg = "";
        $msg = "Вы (или кто-то, указавший вашу почту) запросили восстановление пароля.\n";
        $msg.= "Если вы действительно хотите восстановить пароль, перейдите по ссылке ниже\n";
        $msg.= $url;
        $user->mailer()
            ->message($msg)
            ->subject("Восстановление пароля")
            ->code("user/passwordRecovery")
            ->param("changePasswordURL",$url)
            ->send();
        
        $user->log("Запрос восстановления пароля");
        mod::action("user_action","lostSend")->redirect();
    }

    /**
     * Команда установки нового пароля, по ссылке для восстановления
     * Если пользователь не был подтвержден, подтверждаем почту
     **/
    public function post_changePassword($p) {

        $user = user::get($p["id"]);
        
        if(!$user->testCode($p["code"])) {
            mod::msg("Неверный код подтверждения",1);
            return;
        }
        
        $user->changePassword($p["password"]);
        $user->activate();
        $user->setVerification();
        $user->newCode();
        mod::action("user_action","passwordChanged")->redirect();
    }
    
    /**
     * @return Возвращает ссылку для восстановления пароля
     **/
    public function changePasswordURL($user) {
        $ret = "http://";
        $ret.= mod_url::current()->server();
        $ret.= mod_action::get("user_action","newPassword",array(
            "id" => $user->id(),
            "code" => $user->newCode(),
        ))->url();
        return $ret;
    }

}
