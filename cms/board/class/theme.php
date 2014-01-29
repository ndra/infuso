<?

/**
 * Стандартная тема для интернет-магазина
 **/

class board_theme extends tmp_theme {

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
