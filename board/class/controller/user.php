<?

class board_controller_user extends mod_controller {

    public function postTest() {
        return user::active()->exists();
    }

    /**
     * Контроллер получения списка пользователей для выбиралки
     **/
    public function post_getUserList($p) {

        user::active()->checkAccessThrowException("board/getUserList");

        $ret = array();

        foreach(user::all()->eq("verified",1) as $user) {
            $ret[] = array(
                "id" => $user->id(),
                "text" => $user->data("email"),
            );
        }

        return $ret;

    }

}
