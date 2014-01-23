<?

namespace mod\template;
use infuso\core;

/**
 * Базовый класс для шаблонов, виджетов в пр.
 **/
abstract class generic extends core\component {

    public function __invoke() {
        $args = func_get_args();
        return call_user_func_array(array($this,"exec"),$args);
    }

    abstract public function exec();

    /**
     * Выполняет шаблон или виджет и возвращает результат ввиде строки
     **/
    public function rexec() {
        ob_start();
        $this->exec();
        return ob_get_clean();
    }

    public function delayed() {
        echo $this->delayedMarker();
    }

    public function delayedMarker() {
    
        $params = $this->params();
        $params["*delayed"] = false;
    
        return \tmp_delayed::add(array(
            "class" => "tmp_generic",
            "method" => "execStatic",
            "arguments" => array(
                get_class($this),  
                $params,              
            ),
        ));
    }
    
    public static function execStatic($class,$p) {
    
        $generic = new $class;
        $generic->params($p);
        $generic->exec();
    
    }

}
