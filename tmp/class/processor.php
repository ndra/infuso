<?

namespace mod\template;
use infuso\core;

/**
 * Класс процессора шаблонов
 **/
class processor extends core\component {

	/**
	 * Список шаблонов в регионах
	 **/
    private $regions = array();
    
    /**
     * Текущая модель
     **/
    private $ar = null;
    
    private $conveyor = array();
    
    /**
     * @return Возващает объект шаблона
     **/
    public function template($name,$params=array()) {
        $tmp = new template($name,$this);
        $tmp->params($params);
        return $tmp;
    }
    
    /**
     * $params - массив аргументов функции
     * Если в этом массиве один элемент и он - массив, возвращаем этот массив
     * Если элементов больше чем один, складываем их в массив с ключами p1,p2,p3...
     **/
    public static function normalizeArguments($arguments) {

        if(sizeof($arguments)==0) {
            $ret = template::currentParams();
            return $ret;
        }

        $ret = array();
        foreach(array_values($arguments) as $key=>$val) {
            $ret["p".($key+1)] = $val;
        }

        if(sizeof($arguments)==1) {
            $a = end($arguments);
            if(is_array($a))
                foreach($a as $key=>$val)
                    $ret[$key] = $val;
        }

        return $ret;
    }
    
    /**
     * Выполняет шаблон
     **/
    public function exec($name) {
    
        $template = self::template($name);
        $args = func_get_args();
        array_shift($args);
        $args = self::normalizeArguments($args);
        foreach($args as $key=>$val) {
            $template->param($key,$val);
        }
        $template->exec();
    
    }
    
    /**
     * Добавляет шаблон $name в блок $block
     **/
    public function add($block,$name) {

        if(is_object($name)) {
            tmp_block::get($block)->add($name);
        } else {

            $p = func_get_args();
            $name = template::handleName($name);
            $template = $this->template($name);
            array_shift($p);
            array_shift($p);
            $params = self::normalizeArguments($p);

            foreach($params as $key=>$val) {
                $template->param($key,$val);
            }

            \tmp_block::get($block)->add($template);
        }

    }
    
    /**
     * Возвращает текущий конвеер
     **/
    public function conveyor() {
        if(!count($this->conveyor)) {
            $this->conveyor[] = new conveyor();
		}
        return end($this->conveyor);
    }
    
    /**
     * Создает новую область видимости
     **/
    public function pushConveyor() {
        $this->conveyor[] = new tmp_conveyor();
    }

    /**
     * Уничтожает текущую область видимости, применяя ее свойства к предыдущей
     **/
    public function mergeConveyorDown() {
        $conveyor = $this->popConveyor();
        $this->conveyor()->mergeWith($conveyor);
        return $conveyor;
    }

    public function popConveyor() {
        $conveyor = array_pop($this->conveyor);
        return $conveyor;
    }

    public function destroyConveyors() {
        $this->conveyor = array();
    }
    
    /**
     * Добавляет css автоматически
     **/
    public function css($path,$priority = 0){
        if($path{0}=="/") {
            $this->packCSS($path,$priority);
        } else {
            $this->singleCSS($path,$priority);
		}
    }

    /**
     * Добавляет css без упаковки
     **/
    public function singleCSS($path,$priority=null) {
        $this->conveyor()->add(array(
            "t" => "sc",
            "c" => $path,
            "p" => $priority,
        ));
    }

    /**
     * Добавляет упакованный css
     **/
    public function packCSS($path,$priority=null) {
        $this->conveyor()->add(array(
            "t" => "c",
            "c" => $path,
            "p" => $priority,
        ));
    }

    /**
     * Добавляет js автоматически
     **/
    public function js($path,$priority=null) {
        if($path{0}=="/") {
            $this->packJS($path,$priority);
        } else {
            $this->singleJS($path,$priority);
		}
    }

    /**
     * Добавляет js без упаковки
     **/
    public function singleJS($path,$priority=null) {
        $this->conveyor()->add(array(
            "t" => "sj",
            "c" => $path,
            "p" => $priority,
        ));
    }

    /**
     * Добавляет упакованный js с упаковкой
     **/
    public function packJS($path,$priority=null) {
        $this->conveyor()->add(array(
            "t" => "j",
            "c" => $path,
            "p" => $priority,
        ));
    }
    
    /**
     * Добавляет строку в хэд
     **/
    public function head($str,$priority=null) {
        $this->conveyor()->add(array(
            "t" => "h",
            "c" => $str,
            "p" => $priority,
        ));
    }

    /**
     * Добавляет в хэдер скрипт (js-код)
     **/
    public function script($str,$priority=null) {
        $this->conveyor()->add(array(
            "t" => "s",
            "c" => $str,
            "p" => $priority,
        ));
    }
    
    public function header() {
        $this->exec("/tmp/header");
    }

    public function footer() {
        echo "</body></html>";
    }
    
	public function jq() {
        \tmp_lib::jq();
    }

}
