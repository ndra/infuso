<?

/**
 * Модель пользователя
 **/
class user extends reflex {

    private static $activeUser = null;

    private $thisIsActiveUser = false;

    private $errorText = "";

    /**
     * Удаляем неактивных пользователей через 2 дня после регистрации
     **/
    public function reflex_cleanup() {
        if(!$this->verified()) {
            // Определяем, сколько дней назад зарегистрировался пользователь
            $stamp = util::date($this->data("registrationTime"))->stamp();
            $d = (util::now()->stamp() - $stamp)/24/3600;
            if($d>14)
                return true;
        }
    }

    /**
     * Триггер перед созданием
     * Запоминаем время создания
     **/
    public final function reflex_beforeCreate() {
        $this->data("registrationTime",util::now());
    }

    /**
     * Возвращает коллекцию всех пользователей
     **/
    public static function all() {
        return reflex::get(get_class())->desc("registrationTime");
    }

    /**
     * @return Возвращает пользователя по id
     **/
    public static function get($data) {
        return reflex::get(get_class(),$data);
    }

    /**
     * Возвращает пользователя по адресу электронной почты
     **/
    public static final function byEmail($email) {

        // Если вдруг у пользователя не будет почты, мы не должны под ним логиниться
        // На всякий случай делаю проверку
        if(!trim($email)) {
            return user::get(0);
        }

        return self::all()->eq("email",$email)->one();
    }

    /**
     * Создает виртуального пользователя (без занесения в базу)
     **/
    public final function virtual($data=null) {
        return reflex::virtual("user",$data);
    }

    /**
     * Создает и возвращает нового пользователя на основе массива данных
     * $p["password"] - пароль, который потом зашифруеттся. В явном виде пароль в базе не хранится
     * $p["email"] - электронная почта пользователя
     **/
    public static function create($p) {

        // Удаляем пробельные символы вокруг логина и пароля
        if(!$p["password"] = self::checkAbstractPassword($p["password"])) {
            return user::get(0);
        }

        // Проверяем электронную почту
        if(!$p["email"] = self::normalizeEmail($p["email"])) {
            mod::msg("Ошибка в адресе электронной почты",1);
            return user::get(0);
        }

        // Ищем пользователя с такой электронной почтой
        if(user::byEmail($p["email"])->exists()) {
            mod::msg("Пользователь с такой электронной почтой уже существует",1);
            return user::get(0);
        }

        self::$password = $p["password"];
        $p["password"] = mod_crypt::hash($p["password"]);

        foreach(user::virtual()->fields() as $field)
            if($field->editable())
                $insert[$field->name()] = $p[$field->name()];
                
        $insert["password"] = $p["password"];
        $insert["email"] = $p["email"];
        $insert["registrationTime"] = util::now()."";

        $user = reflex::create("user",$insert);

        return $user;
    }

    private static $password = null;

    /**
     * Возвращает пароль у вновь созданого пользователя.
     * Данная функция будет работать в пределах того скрипта в котором был создан пользователь.
     **/
    public final function password() {
        return self::$password;
    }

    /**
     * Проверяет код, сгенерированный методом newCode
     **/
    public function testCode($code) {
        list($stamp,$tail) = explode(":",$code);
        $stamp = intval($stamp);

        if(strlen($tail)!=20)
            return false;

        // Сколько часов назад был создан код
        $d = (util::now()->stamp() - $stamp)/3600;
        if($d<0)
            return false;
        if($d>1)
            return false;

        return $this->data("verificationCode")==$code;
    }

    /**
     * генерирует код
     **/
    public function newCode() {
        $code = util::now()->stamp().":".util::id(20);
        $this->data("verificationCode",$code);
        return $code;
    }

    /**
     * Подтверждает почту пользователя без дополнительных проверок
     **/
    public final function setVerification() {
        if(!$this->data("email")) return this;
        $this->data("verified",1);
        $this->log("Адрес электронной почты подтвержден");
        return $this;
    }

    /**
     * Снимает подтверждение почты пользователя
     **/
    public final function removeVerification() {
        $this->data("verified",0);
        return $this;
    }

    /**
     * @return Возвращает флаг подтверждения пользователя (true/false)
     **/
    public final function verified() {
        return !!$this->data("verified");
    }

    /**
     * @return Меняет пароль рользоватея
     **/
    public final function changePassword($pass) {
        $pass = self::checkAbstractPassword($pass);
        if(!$pass)
            return false;
        $this->data("password",mod_crypt::hash($pass));
        $cookie = $_COOKIE["login"];
        $this->authorizations()->neq("cookie",$cookie)->delete();
        return true;
    }

    /**
     * Меняет электронную почту пользователя
     **/
    public final function changeEmail($email) {

        if(!$email = self::normalizeEmail($email)) {
            mod::msg("Ошибка в адресе электронной почты",1);
            return false;
        }

        if($email==$this->data("email"))
            return true;

        if(user::byEmail($email)->exists()) {
            mod::msg("Пользователь с такой электронной почтой уже существует",1);
            return false;
        }

        $this->data("email",$email);
        return true;
    }

    /**
     * Возвращает активного (залогиневшегося) пользователя
     * Если пользователь не залогинен, возвращается несуществующий объект
     **/
    public static final function active() {

        if(!self::$activeUser) {

            $cookie = $_COOKIE["login"];

            if(strlen($cookie)>5) {
                $auth = user_auth::all()->eq("cookie",$cookie)->one();
            } else {
                $auth = user_auth::get(0);
            }

            $user = $auth->user();

            if(!$user->verified()) {
                $user = user::virtual();
            }

            if(!$user->exists()) {
                $user = user::virtual();
            }

            self::$activeUser = $user;
            $user->thisIsActiveUser = true;
        }

        return self::$activeUser;

    }

    /**
     * Является ли этот пользователь активным (тем, что залогинен)
     **/
    public function isActiveUser() {

        if($this->exists() && $this->id()==user::active()->id()) {
            return true;
		}

        if($this->thisIsActiveUser) {
            return true;
		}

        return false;

    }

    /**
     * Генерирует пользователю новый код для куков, устанавливает его и возвращает его же
     **/
    private final function newCookie() {
        $cookie = util::id();
        $auth = reflex::create("user_auth",array(
            "cookie" => $cookie,
            "userID" => $this->id(),
        ));
        return $cookie;
    }


    /**
     * Пытается выполнить вход по данному логину (электронной почте) и паролю.
     * Возвращает true/false
     **/
    public static final function login($login,$pass,$keep=null) {
    
        $login = strtolower(trim($login));
        $pass = trim($pass);
        $user = user::byEmail($login);
        
        if(!$user->verified()) {
            return false;
        }
        
        if($user->checkPassword($pass)) {
            $user->activate($keep);
            return true;
        }
        
        return false;
            
    }

    /**
     * Активирует пользователя без каких-либо проверок
     **/
    public final function activate($keep=null) {
        if(!$this->exists())
            return;
        $keepDays = $keep ? 14 : null;
        $cookie = $this->newCookie();
        $expire = $keepDays ? time()+60*60*24*$keepDays : null;
        setcookie("login",$cookie,$expire,"/");
        $_COOKIE["login"] = $cookie;
        self::$activeUser = $this;
        $this->log("Вход");
    }

    /**
     * Деактивирует пользователя активного пользователя
     **/
    public static final function logout() {

        $user = user::active();
        $user->store();
        $user->log("Выход");

        $cookie = $_COOKIE["login"];
        $user->authorizations()->eq("cookie",$cookie)->delete();

        self::$activeUser = null;
    }

    /**
     * Возвращает коллекцию авторизаций пользователя
     **/
    public function authorizations() {
        return user_auth::all()->eq("userID",$this->id());
    }

    /**
     * Проверяет, может ли пользователь выполнить операцию $operation с парамтерами $params
     **/
    public final function checkAccess($operationCode,$params=array()) {

        mod_profiler::beginOperation("user","checkAccess",$operationCode);
    
        if(!is_array($params)) {
            throw new Exception("user::checkAccess() second argument must be array");
        }
    
        $this->clearErrorText();
        $operation = user_operation::get($operationCode);
        $ret = $operation->checkAccess($this,$params);
        
        if(!$this->errorText()) {
            $this->setErrorText("Операция {$operationCode} отклонена");
        }        
        
        mod_profiler::endOperation();
        
        return !!$ret;
    }

    /**
     * Проверяет возможность выполнить операцию.
     * Если в доступе отказано, выкидывает Исключение
     **/
    public function checkAccessThrowException($operationCode,$params=array()) {

        if(!$this->checkAccess($operationCode,$params)) {
            throw new Exception($this->errorText());
        }
    }
    
    public function clearErrorText() {
        $this->errorText = "";
    }    
   
    public function setErrorText($errorText) {
        if(trim($errorText)) {
            $this->errorText = $errorText;
        }
        return $this;        
    }
    
    public function errorText() {
        return $this->errorText;
    }

    private $roles = null;

    /**
     * Возвращает массив ролей данного пользователя
     **/
    public function roles() {

        mod_profiler::beginOperation("user","roles",1);

        if($this->roles===null) {

            $this->roles = array();

            $this->roles[] = user_role::get("guest");

            foreach(util::splitAndTrim($this->data("roles")," ") as $role) {
                $role = user_role::get($role);
                if($role->exists() && $role->code()!="guest") {
                    $this->roles[] = $role;
                }
            }

        }

        mod_profiler::endOperation();

        return $this->roles;
    }

     /**
     * Проверяет есть ли роль у юзера по коду
     **/
    public function hasRole($roleCode) {
        if(!is_string($roleCode)) {
            throw new Exception("user::hasRole() first argument must be a string");
        }
        $roles = $this->roles();
        foreach($roles as $role) {
          if($roleCode == $role->code()){
              return true;
          }  
        }       
        
        return false;      
    }

    /**
     * Удаляет роль у пользователя
     **/
    public function removeRole($roleToDelete) {
        $roles = array();
        foreach($this->roles() as $role)
            if($role->code()!=$roleToDelete)
                $roles[] = $role->code();
        $this->data("roles",implode(" ",$roles));
        $this->roles = null;
    }

    /**
     * Добавляет пользователю роль
     **/
    public function addRole($role) {
        $roles = array($role);
        foreach($this->roles() as $role) {
            $roles[] = $role->code();
        }
        $roles = array_unique($roles);
        $this->data("roles",implode(" ",$roles));
        $this->roles = null;
    }

    /**
     * Отправляет пользователю письмо
     **/
    public final function mail($first,$second=null) {

        if(func_num_args()==1 && is_array($first)) {
            $params = $first;
        } else {
            $params = array(
                "message" => $first,
                "subject" => $second,
            );
        }

        $mail = $this->mailer();
        $mail->params($params);
        $mail->send();
    }

    /**
     * Mail builder для отправки письма текущему пользователю
     *
     * @return Class user_mail
     * @author Petr.Grishin
     **/
    public function mailer() {
    
        $mail = new user_mailer();
        $mail->userID($this->id());
        $mail->to($this->data("email"));
        
        //Параметры по умолчанию
        $mail->from(mod::conf("user:email_from"));
        $mail->layout(mod::conf("user:email_template"));
        
        if($this->param("disableMailer")) {
            $mail->param("disable",true);
        }

        return $mail;
    }
    
    /**
     * Временно блокирует отправку писем пользователю
     **/
    public function disableMailer() {
        $this->param("disableMailer",true);
    }
    
    /**
     * Снимает блокировку отправки писем пользователю
     **/
    public function enableMailer() {
        $this->param("disableMailer",false);
    }
    
    /**
     * Возвращает коллекцию писем, отправленных этому пользователю
     * (Если перехват писем включен в настройках)
     **/
    public function mailMessages() {    
        $userID = $this->id();
        if(!$this->exists())
            $userID = -1;
        return user_mail::all()->eq("userID",$userID);    
    }
    
    /**
     * Делает все письма прочитанными
     **/
    public function markAllMessagesRead() {
        $this->mailMessages()->data("read",true);
    }

    /**
     * Приводит адрес электронной почты к какноническому виду.
     * Возвращает null, если прверка по режексу не удалась
     **/
    public static final function normalizeEmail($email) {
        $email = strtolower(trim($email));
        $s = "1234567890qwertyuiopasdfghjklzxcvbnm\.\-\_";
        $r = preg_match("/^[$s]+@[$s]+$/",$email,$m);
        return $r ? $email : false;
    }

    /**
     * Проверяет пароль на соответствие требованиям безопасности (минимальная длина и т.п.)
     * Проверкой пароля конкретного пользователя этот метод не занимается
     **/
    public static final function checkAbstractPassword($password) {
        $password = trim($password);
        $passlen = intval(mod::conf("user:passwordLength"));
        if($passlen<1) $passlen = 1;
        if(strlen($password)<$passlen) {
            mod::msg("Слишком короткий пароль. Минимальное количество символов: $passlen",1);
            return false;
        }
        return $password;
    }

    /**
     * Возвращает все параметры конфигурации
     **/
    public static function configuration() {
        return array(
            array("id"=>"user:passwordLength","title"=>"Минимальная длина пароля"),
            array("id"=>"user:email_template","title"=>"Шаблон письма","type"=>"textarea","descr"=>"Используйте формат Шапка %text% Подвал"),
            array("id"=>"user:email_from","title"=>"Отправитель письма","type"=>"textfield","descr"=>"То, что будет стоять в поле «От» писем, пришедших с сайта."),
        );
    }

    public function extra($key,$val=null) {
        $extra = $this->pdata("extra");
        if(func_num_args()==1) {
            return $extra[$key];
        }
        if(func_num_args()==2) {
            $extra[$key] = $val;
            $this->data("extra",json_encode($extra));
        }
    }

    /**
     * Возвращает значение поля пользователя или результат выполнения
     * соответствующего метода поведений
     **/
    private final function fieldOrBehaviour($key) {
        $val = trim($this->data($key));
        if($val)
            return $val;

        foreach($this->behaviours() as $b)
            if(method_exists($b,$key))
                if($val = trim($b->$key()))
                    return $val;
    }

    /**
     * Возвращает электронную почту пользователя
     **/
    public function email() {
        return $this->fieldOrBehaviour("email");
    }

    /**
     * Возвращает телефон
     **/
    public function phone() {
        return $this->fieldOrBehaviour("phone");
    }

    /**
     * Возвращает имя пользователя
     * Вернет значения из поля "firstName" или информацию из поведений, если в поле пусто
     **/
    public function firstName() {
        return $this->fieldOrBehaviour("firstName");
    }

    /**
     * Возвращает фамилию польщователя
     * Вернет значения из поля "lastName" или информацию из поведений, если в поле пусто
     **/
    public function lastName() {
        return $this->fieldOrBehaviour("lastName");
    }

    /**
     * Возвращает ник пользователя
     * Вернет значения из поля "nickName" или информацию из поведений, если в поле пусто
     **/
    public function nickName() {
        return $this->fieldOrBehaviour("nickName");
    }

    /**
     * Возвращает город пользователя
     * Вернет значения из поля "city" или информацию из поведений, если в поле пусто
     **/
    public function city() {
        return $this->fieldOrBehaviour("city");
    }

    /**
     * Возвращает регион пользователя
     * Вернет значения из поля "region" или информацию из поведений, если в поле пусто
     **/
    public function region() {
        return $this->fieldOrBehaviour("region");
    }

    /**
     * Возвращает страну пользователя
     * Вернет значения из поля "country" или информацию из поведений, если в поле пусто
     **/
    public function country() {
        return $this->fieldOrBehaviour("country");
    }

    /**
     * Системная функция
     * Вызывается когда пользователь просматривает страницу
     **/
    public function registerActivity() {
        // Округляем время до 5 минут и записываем в пользователя
        $stamp = util::now()->stamp();
        $stamp = round($stamp/60/5)*60*5;
        $this->data("lastActivity",$stamp);
    }

}
