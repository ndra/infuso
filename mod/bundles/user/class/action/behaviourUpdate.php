<?

/**
 * Поведение, реализующее функции изменения регистрационной информации
 **/
class user_action_behaviourUpdate extends mod_behaviour {

    public function behaviourPriority() {
        return -1;
    }

    public function addToClass() {
        return "user_action";
    }

    /**
     * Экшн обновления информации пользователя
     **/
    public function index_update() {
        tmp::noindex();
        if(!user::active()->exists()) {
            header("location:/");
            die();
        }
        tmp::exec("/user/update");
    }

    /**
     * Метод фильтрует пользовательские данные
     * и возвращает массив данных, которые передадутся пользователю для обновлении информации
     * Данная функция оставит в исходном массиве только поля email и password
     **/
    public function filterUpdateFields($data) {
        return util::filter($data,"email,password,firstName,lastName");
    }

    /**
     * Команда обновления информации о пользователе
     **/
    public function post_update($p) {

        $form = form::byCode("user:update");
        if(!$form->validate($p))
            return;

        $data = $this->component()->filterUpdateFields($p);

        $user = user::active();

        foreach($data as $key=>$val) {
            switch($key) {
                case "email":
                    if($data["email"])
                        $user->changeEmail($data["email"]);
                    break;
                case "password":
                    if($data["password"])
                        $user->changePassword($data["password"]);
                    break;
                default:
                    $user->data($key,$val);
            }
        }

        $user->log("Изменение данных (самостоятельно)");
        mod::msg("Данные изменены");
    }

}
