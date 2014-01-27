<?

/**
 * Контроллер крона
 **/
class mod_cron extends mod_controller {

    public static function indexTest() {
        return true;
    }

    public static function index() {
        mod::service("cron")->checkTimeAndprocess();
    }

}
