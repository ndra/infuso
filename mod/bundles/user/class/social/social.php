<?

/**
 * Модель авторизации в социальной сети
 **/
class user_social extends reflex {

    private static $sessionKey = "user:social";

    /**
     * Возвращает коллекцию всех элементов
     **/
    public static function all() {
        return reflex::get(get_class());
    }

    /**
     * @return Возвращает элемент по id
     **/
    public static function get($id) {
        return reflex::get(get_class(),$id);
    }

    /**
     * Активирует социальный профиль в данной сессии
     **/
    public function addActive($social) {
        mod::session(self::$sessionKey,$social->id());
    }

    /**
     * Возвращает активный социальный профиль
     **/
    public function active() {
        return self::all()->eq("id",mod::session(self::$sessionKey))->one();
    }

    /**
     * @return Возвращает пользователя, к которому привязан данный социальный профиль
     **/
    public function user() {
        return $this->pdata("userID");
    }

    public function reflex_parent() {
        return $this->user();
    }

    /**
     * Прикрепляет данный социальный профиль к активному пользователю
     **/
    public static function appendToActiveUser() {

        $user = user::active();
        if(!$user->exists()) {
            return;
        }

        if(!mod::session(self::$sessionKey)) {
            return;
        }

        self::active()->data("userID",$user->id());
        $_SESSION[self::$sessionKey] = null;

    }

    public function reflex_title() {
        return $this->data("identity");
    }

    /**
     * Возвращает данные, предоставленные социальной сетью ввиде массива
     **/
    public function socialData($key=null) {
        $ret = $this->pdata("data");
        if($key)
            $ret = $ret[$key];
        return $ret;
    }

    /**
     * Возвращает юзерпик пользователя
     **/
    public function userpick() {
        return $this->socialData("photo_big");
    }

    /**
     * Возвращает имя пользователя
     **/
    public function firstName() {
        return $this->socialData("first_name");
    }

    /**
     * Возвращает фамилию
     **/
    public function lastName() {
        return $this->socialData("last_name");
    }

    /**
     * Возвращает название социальной сети, к которой принадлежит данный профиль
     * на русском языке
     **/
    public function networkName() {
        $allNets = array(
            "vkontakte" => "Вконтакте",
            "odnoklassniki" => "Одноклассники",
            "mailru" => "Mail.ru",
            "facebook" => "Facebook",
            "twitter" => "Twitter",
            "google" => "Google",
            "yandex" => "Яндекс",
            "livejournal" => "LiveJournal"
        );
        
        return $allNets[$this->socialData("network")];
    }


    /**
     * Возвращает строку, идентифицирующую социальный профиль
     * Это может быть ник пользователя или ссылка на профиль в соцсети
     * Возвращаемое значение определяется социальной сетью
     **/
    public function identity() {
        return $this->socialData("identity");
    }
    
    /**
     * Возвращает название социальной сети, к которой принадлежит данный профиль
     **/
    public function network() {
        return $this->socialData("network");
    }
    
    public function icon16() {
        return "/user/res/social-icons-16/".$this->network().".png";
    }
    
    public function reflex_beforeStore() {
        $this->data("network", $this->network());
    }

}
