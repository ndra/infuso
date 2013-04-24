<?

class board_controller_informer extends mod_controller {

    public static function indexTest() {
        return user::active()->exists();
    }
    
    public static function index() {
        tmp::exec("/board/informer");
    }

}
