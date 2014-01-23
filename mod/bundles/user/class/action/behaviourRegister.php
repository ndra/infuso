<?

/**
 * Поведение, реализующее функции регистрации
 **/
class user_action_behaviourRegister extends mod_behaviour {

    public function behaviourPriority() {
        return -1;
    }

    public function addToClass() {
        return "user_action";
    }

    /**
     * Экшн регистрации пользователя
     **/
    public function index_register() {
        tmp::noindex();
        tmp::exec("/user/register");
    }
    
    /**
     * Экшн страницы, сообщающей об успешной регистрации
     **/
    public function index_registerComplete() {
        tmp::noindex();
        tmp::exec("/user/registerComplete");
    }
    
    /**
     * Экшн страницы подтверждения электронной почты
     **/
    public function index_verification($p) {

        tmp::noindex();
        $user = user::get($p["id"]);

        // Если пользователь не существует, показываем ошибку верификации
        if(!$user->exists()) {
            tmp::exec("/user/verificationFailed");
            return;
        }

        // Если пользователь подтвержден, показываем ошибку верификации
        if($user->verified()) {
            tmp::exec("/user/verificationFailed");
            return;
        }

        // Если пользователь подтвержден, показываем страницу успешной верификации
        $r = $user->testCode($p["code"]);
        if($r) {
            $user->setVerification();
            $user->activate();
            $user->mailer()
                ->subject("Ваша учетная запись активирована")
                ->message("Поздравляем, ваша учетная запись активирована")
                ->code("user/verification")
                ->send();

            mod_action::get("user_action","verificationComplete")->redirect();
            mod::fire("userVerificatrionComplete");

        }

        tmp::exec("/user/verificationFailed");

    }
    
    public function index_verificationResend() {
        tmp::exec("/user/verificationResend");
    }
    
    public function post_resendVerification($p) {

        $user = user::byEmail($p["email"]);
        $flag = self::sendVerificationMessage($user);
        mod_action::get("user_action","verificationSent")->redirect();

    }
    
    /**
     * Экшн страницы, сообщающей об отправке ссылки на подтверждение почты
     **/
    public function index_verificationSent() {
        tmp::noindex();
        tmp::exec("/user/verificationSent");
    }

    /**
     * Экшн сообщения о завершении верификации
     **/
    public function index_verificationComplete() {
        tmp::noindex();
        tmp::exec("/user/verificationComplete");
    }
    
    /**
     * @return string
     * Устанавливает пользователю новый код авторизации и возвращает адрес страницы подтверждения почты
     **/
    public function verificationURL($user) {
    
        $user->newCode();
    
        $ret = "http://";
        $ret.= mod_url::current()->server();
        $action = mod_action::get("user_action","verification",array(
            "id" => $user->id(),
            "code" => $user->newCode(),
        ));
        $ret.= $action->url();
        return $ret;
    }

    /**
     * Метод фильтрует пользовательские данные
     * и возвращает массив данных, которые передадутся пользователю для регистрации
     * Данная функция оставит в исходном массиве только поля email и password
     **/
    public function filterRegisterFields($data) {
        return util::filter($data,"email,password,firstName,lastName");
    }
    
    /**
     * return boolean
     * Отправляет пользователю ссылку на подтверждение почты
     **/
    public function sendVerificationMessage($user) {
    
        // Если пользователь не существует, ссылку на подтверждение не высылаем
        if(!$user->exists()) {
            return false;
        }
    
        // Если почта пользователя уже подтверждена, второй раз ссылку на подтверждение не высылаем
        if($user->verified()) {
            return false;
        }
    
        // Отправляем пользователю сообщение о регистрации
        $txt = "";
        $url = self::verificationURL($user);
        $txt.= "Для подтверждения регистрации перейдите по ссылке\n$url\n";
        
        $user->mailer()
            ->message($txt)
            ->subject("Регистрация")
            ->code("user/registration")
            ->param("verificationURL",$url)
            ->send();
            
        $user->log("Регистрация");
        $user->data("self-registered",true);
        
        return true;
    }

    /**
     * Команда регистрации пользователя
     **/
    public function post_register($p) {

        // Валидируем форму
        $form = form::byCode("user:register");
        if(!$form->validate($p))
            return;

        // Создаем пользоватебя в БД
        $data = $this->component()->filterRegisterFields($p);
        $user = user::create($data);
        if(!$user->exists())
            return;

        $this->sendVerificationMessage($user);

        $action = mod_action::get("user_action","registerComplete");
        header("location:{$action->url()}");
    }

}
