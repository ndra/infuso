<?

class user_action extends mod_controller {

    /**
     * Включаем видимость класса в браузере
     **/
    public function indexTest() {
        return true;
    }

    /**
     * Видимость сайта для команд POST
     **/
    public function postTest() {
        return true;
    }

	/**
	 * Проверяет, свободен ли email
	 * Используется как callback в формах
	 **/
	public function registerCheckEmail($email) {

	    if($email==user::active()->data("email"))
	        return true;

	    $user = user::byEmail($email);
	    if($user->exists()) {
	    
	        if(!$user->verified()) {
	            $resendURL = mod::action("user_action","verificationResend")->url();
	            return "Заявка на регистрацию с почтой $email уже существует. <a href='$resendURL' >Получить код подтверждения?</a> ";
	        }
	    
	        return "Пользователь с электронной почтой $email уже существует.";
	    }
	    
	    return true;
	}

	/**
	 * Редиректим пользователя после авторизации
	 *
	 **/
	public function redirectAfterLogin() {
	
		$this->redirect("location:/");
		
        // Выбрасываем событие редиректа при авторизации
        mod::fire("user_afterloginRedirect");
	}

} //END CLASS
