<?

/**
 * Поведение по умолчанию для пользователя
 **/
class user_behaviour extends mod_behaviour {

	public function behaviourPriority() {
	    return -1;
	}
	
	public function addToClass() {
	    return "user";
	}

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

	/**
	 * Метод, возвращающий имя объекта для рефлекса
	 * Используется при вызове $this->title();
	 **/
    public function reflex_title() {
    
        $ret = trim($this->firstName()." ".$this->lastName());
    
        if(!$ret) {
            $ret = "{$this->data(email)}";
        }
            
        if(!$ret) {
            $ret = "user:{$this->id()}";
		}
            
        return $ret;
    }
    
    /**
     * Возвращает объект файла юзерпика пользователя
     **/
    public function userpick() {

        if($this->data("userpick")!="") {
			return $this->pdata("userpick");
        }

        $key = "userpick";
        foreach($this->behaviours() as $b) {
            if(get_class($b) != get_class($this)) {
	            if(method_exists($b,$key)) {
	                if($val = trim($b->$key())) {
	                    return file::get($val);
	                }
	            }
            }
        }

        return file::nonExistent();
    }
    
}
