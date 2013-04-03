<?

/**
 * Поведение по умолчанию для пользователя
 **/
class user_behaviour extends mod_behaviour {

    public function reflex_table() {
        return "user_list";
    }

    public function reflex_classTitle() {
        return "Пользователь";
    }

    public function reflex_children() {
        return array(
            $this->authorizations()->param("menu",false)->title("Авторизаци"),
            $this->mailMessages()->param("menu",false)->title("Письма"),
        );
    }

    /**
     * Проверяет пароль $pass для данного полбзователя
     * Возвращает true/false
     **/
    public function checkPassword($pass) {
        $check = mod_crypt::checkHash($this->data("password"),$pass);
        return $check;
    }

    public function reflex_title() {
    
        $ret = trim($this->firstName()." ".$this->lastName());
    
        if(!$ret) {
            $ret = "{$this->data(email)}";
        }
            
        if(!$ret)
            $ret = "user:{$this->id()}";
            
        return $ret;
    }

}
