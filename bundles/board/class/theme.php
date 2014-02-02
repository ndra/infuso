<?

namespace Infuso\Board;

/**
 * Стандартная тема для интернет-магазина
 **/

class Theme extends \tmp_theme {

    public function path() {
        return self::inspector()->bundle()->path()."/theme/";
    }
    
    public function base() {
        return "board";
    }
    
    public function autoload() {
        return true;
    }
    
    public function name() {
        return "Стандартная тема board";
    }

}
