<?

/**
 * Поведение, реализующее функции логина-логаута
 **/
class user_action_behaviour extends mod_behaviour {

    public function behaviourPriority() {
        return -1;
    }

    public function addToClass() {
        return "user_action";
    }

    /**
     * Экшн входа
     **/
    public function index_login() {
        tmp::noindex();
        tmp::exec("/user/login");
    }

    /**
     * Экшн выхода
     *
     * @todo После перенаправления возможно нужно выбрасывать событие, как при авторизации
     **/
    public function index_logout() {
        tmp::noindex();
        user::active()->logout();
        header("location:/");
    }

    /**
     * Команда выхода. Делает активного пользователя неактивным.
     **/
    public function post_logout() {
        user::active()->logout();
    }

    /**
     * Команда авторизации
     **/
    public function post_login($p) {
    
        if(user::login($p["login"],$p["password"],$p["keep"])) {
            user_action::redirectAfterLogin();
            return true;
        }
        
        return false;
    }

}
