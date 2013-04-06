<?

/**
 * Класс для работы с массивами в ООП-стиле
 **/
class util_array extends mod_component {

    private $data = array();

    public function __construct(&$data) {
        $this->data = &self::toNativeArray($data);
    }

    /**
     * Возвращает массив (не класс)
     **/
    public function &asArray() {
        return $this->data;
    }

    /**
     * Выводит массив в человекопонятной форме
     * Использует шаблон /util/array
     **/
    public function prettyPrint() {
        tmp::exec("/util/array",array(
            "data" => $this->asArray()
        ));
    }

    /**
     * Преобразует переданное значение в массив
     * массив => массив
     * class util_array => массив
     **/
    private static function &toNativeArray(&$a) {
        if(is_object($a)) {
            if(get_class($a)==get_class()) {
                return $a->asArray();
            }
        }
        return $a;
    }

    /**
     * Геттер
     **/
    public function __get($key) {
        return $this->get($key);
    }

    /**
     * Сеттер
     **/
    public function __set($key,$val) {
        $this->set($key,$val);
    }

    public function set($key,$val) {
        $val = self::toNativeArray($val);
        $this->data[$key] = &$val;
    }

    /**
     * Возвращает элемент массива по ключу
     * Если передано несколько ключей, то они используются как ключи многомерного массива
     **/
    public function get() {

        $ret = &$this->data;

        for($i=0;$i<func_num_args();$i++) {
            $ret = &$ret[func_get_arg($i)];
        }

        if(is_array($ret)) {
            $x = new self($ret);
            return $x;
        }

        return $ret;
    }

    public function push() {

        $a = &$this->data;
        for($i=0;$i<func_num_args()-1;$i++) {
            $a = &$a[func_get_arg($i)];
        }

        $pushed = &func_get_arg(func_num_args()-1);
        $pushed = self::toNativeArray($pushed);
        $a[] = &$pushed;

        if(is_array($pushed)) {
            $ret = new self($pushed);
            return $ret;
        }
    }

    /**
     * Возвращает первый элемент массива
     **/
    public function &first() {

        reset($this->data);
        $key = key($this->data);
        $ret = &$this->data[$key];

        if(is_array($ret)) {
            return new self($ret);
        }

        return $ret;
    }

    /**
     * Возвращает последний элемент массива
     **/
    public function &last() {

        end($this->data);
        $key = key($this->data);
        $ret = &$this->data[$key];

        if(is_array($ret)) {
            return new self($ret);
        }

        return $ret;
    }

    public function eq($ekey,$eval) {
        $ret = array();
        foreach($this->data as $key=>$val) {
            if(is_array($val)) {
                if($val[$ekey] == $eval) {
                    $ret[$key] = &$this->data[$key];
                }
            }
        }
        return new self($ret);
    }

    /**
     * Фильтрует исходный массив, оставляя в нем только элементы с ключами из массива $keys
     **/
    public function filter($keys) {

        if(is_string($keys)) {
            $keys = func_get_args();
        }

        $ret = array();
        foreach($keys as $key) {
            if(array_key_exists($key,$this->data)) {
                $ret[$key] = $this->data[$key];
            }
        }
        return new self($ret);
    }

    public function map($map) {

        $ret = array();

        foreach($map as $srcKey=>$destKey) {
            $ret[$destKey] = $this->data[$srcKey];
        }

        return new self($ret);

    }

}
