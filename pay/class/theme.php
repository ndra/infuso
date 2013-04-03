<?

/**
 * Стандартная тема для интернет-магазина
 **/

class pay_theme extends tmp_theme {

    /**
    * @return Приоритет темы =-1
    **/
    public function priority() {
        return -1;
    }
    
    public function path() {
        return "/pay/theme/";
    }
    
    public function base() {
        return "pay";
    }
    
    public function autoload() {
        return true;
    }
    
    public function name() {
        return "Стандартная тема pay";
    }

}
