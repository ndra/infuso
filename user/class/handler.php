<?

/**
 * Служебный класс - обработчик событий
 **/
class user_handler implements mod_handler {

    public function on_mod_beforeAction() {
        user::active()->registerActivity();
    }
    
    public function on_mod_beforecmd() {
        user::active()->registerActivity();
    }
    
    public static function deleteUnverfiedUsers() {
        mod::service("user")->deleteUnverfiedUsers();        
    }
    
    public static function on_mod_init() {
        reflex_task::add(array(
            "class" => "user_handler",
            "method" => "deleteUnverfiedUsers",
            "crontab" => "0 0 * * *"
        ));
    }

}
