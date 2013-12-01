<?

/**
 * Базовый класс для служб
 **/
class mod_service extends mod_controller {

    public function defaultService() {
        return false;
    }

    public static function serviceFactory() {
        $class = get_called_class();
        return new $class;
    }

}
