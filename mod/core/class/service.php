<?

namespace infuso\core;

/**
 * Базовый класс для служб
 **/
class service extends controller {

    public function defaultService() {
        return false;
    }

    public static function serviceFactory() {
        $class = get_called_class();
        return new $class;
    }

}
