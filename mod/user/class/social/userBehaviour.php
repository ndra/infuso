<?

/**
 * Контроллер авторизации в социальной сети
 **/
class user_social_userBehaviour extends mod_behaviour {

	public function behaviourPriority() {
	    return -10;
	}

	public function addToClass() {
	    //return mod::conf("user:social") ? "user" : null;
	    return "user";
	}

    /**
     * Возвращает социальные профили, подключенные пользователю
     **/
    public function socialLinks() {
        $userID = $this->id();
        if(!$userID)
            $userID = -1;
        return user_social::all()->eq("userID",$userID);
    }

	public function reflex_children() {
	    return array(
	        $this->socialLinks()->title("Социальные сети"),
	    );
	}

	private function searchFieldInActiveSocialLinks($key) {

	    foreach($this->socialLinks() as $link) {
	        $data = $link->pdata("data");
	        if($val = trim($data[$key]))
	            return $val;
	    }

	    foreach(user_social::active() as $link) {
	        $data = $link->pdata("data");
	        if($val = trim($data[$key]))
	            return $val;
	    }
	}

	/**
	 * Возвращает email пользователя
	 **/
	public function email() {
	    return $this->searchFieldInActiveSocialLinks("email");
	}
	
	/**
	 * Возвращает телефон пользователя
	 **/
	public function phone() {
	    return $this->searchFieldInActiveSocialLinks("phone");
	}

	/**
	 * Возвращает имя пользователя
	 **/
	public function firstName() {
	    return $this->searchFieldInActiveSocialLinks("first_name");
	}

	/**
	 * Возвращает фамилию пользователя
	 **/
	public function lastName() {
	    return $this->searchFieldInActiveSocialLinks("last_name");
	}

}
