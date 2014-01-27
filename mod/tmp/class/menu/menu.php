<?

/**
 * Базовый класс для всех меню
 **/
class tmp_menu extends tmp_widget implements iterator {

    // Итераторская шняга
    protected $items = array();
    public function rewind() { reset($this->items); }
    public function current() { return current($this->items); }
    public function key() { return key($this->items); }
    public function next() { return next($this->items); }
    public function valid() { return $this->current() !== false; }

    public function __construct($url=null,$title=null) {
        $this->param("url",$url);
        $this->param("title",$title);
    }

    /**
     * Название виджета
     **/
    public function name() {
        return "Меню";
    }

    /**
     * Добавляет подпункт
     **/
    public function add($url,$title="") {
    
        // Если первый аргумент - объект класса mod_action, преобразуем его в строку
        if(is_object($url) && get_class($url)=="mod_action") {
            $url = $url->url();
		}
		
        $item = new tmp_menu($url,$title);
        
        $this->items[] = $item;
        return $item;
    }

    /**
     * Возвращает подразделы
     **/
    public function items() {
        $ret = $this->items;
        $ret = array_merge($ret,$this->callBehaviours("items"));
        return $ret;
    }

    public function execWidget() {
        foreach($this->items() as $item) {
            echo "<a href='{$item->url()}' >{$item->title()}</a>";
            echo "<br/>";
        }
    }

    public function url($url=null) {
        if(func_num_args()==0) {
            return $this->param("url");
        } else {
            $this->param("url",$url);
            return $this;
        }
    }

    public function title($title=null) {
        if(func_num_args()==0) {
            return $this->param("title");
        } else {
            $this->param("title",$title);
            return $this;
        }
    }

}
