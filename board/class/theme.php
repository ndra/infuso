<?

/**
 * Стандартная тема для интернет-магазина
 **/

class board_theme extends tmp_theme {

    public function path() {
        return "/board/theme/";
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
