<?

class user_service extends mod_service {

 
    public function defaultService() {
        return "user";
    }
    
    public function initialParams() {
        return array(
            "deleteUnverfiedUserDays" => 7,
        );
    }

	public function confDescription() {
	    return array(
	        "components" => array(
	            get_called_class() => array(
	                "params" => array(
	                    "deleteUnverfiedUserDays" => "Через сколько дней удалять пользователей, не подтвердивших почту",
					),
				),
			),
		);
	}
    
    /**
     * Возвращает список юзверов
     **/
    public function users() {
        return user::all();  
    }
    
    
    /**
     * Возвращает список юзверов c неподвержедной почтой
     **/
    public function unverfiedUsers() {
        return $this->users()->eq("verified", 0);  
    }
    
    /**
    * Удяляет всех не активированых пользователей у который рега > deleteUnverfiedUserDays   
    **/
    public function  deleteUnverfiedUsers() {
        $deleteTime = util::now()->shiftDay(-$this->param("deleteUnverfiedUserDays"));
        $users = $this->unverfiedUsers()->lt("registrationTime", $deleteTime);
        $users->delete();
    }
    
}
