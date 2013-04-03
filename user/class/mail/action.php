<?

/**
 * Контроллер, позволяющий пользователю просматривать полученные сообщения
 **/
class user_mail_action extends mod_controller {

    public function indexTest() {
        return true;
    }
    
    public function index() {
        tmp::exec("/user/email-history");
    }

}