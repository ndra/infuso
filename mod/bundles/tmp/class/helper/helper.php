<?

class tmp_helper extends \mod\template\widget {

    private $style = array();

    public function __construct() {
        $this->param("attributes",array());
        $this->param("*style",array());
    }

    public static function fromHTML($html) {
        $xml = end(util::str($html)->html()->body->children());
        $h = new tmp_helper();
        $h->tag($xml->getName());
        foreach($xml->attributes() as $key=>$val) {
            $h->attr($key,$val);
        }
        return $h;
    }

    /**
     * Устанавливает тэг для выбранного элемента
     **/
    public function tag($name) {
        $this->param("tag",$name);
        return $this;
    }

    /**
     * Вызвана без параметров - вернет массив стилей (ключ-значение)
     * Один параметр скаляр - вернет значение стиля для переданного ключа
     * Один параметр массив добавит в элемент массив стилей из массива
     * Два параметра - ключ, значение - добавит стиль
     **/
    public final function style($key=null, $val=null) {

        if(func_num_args()==0) {
            return $this->param("*style");
        }

        if(func_num_args()==1) {

            if(is_array($key) ) {

                foreach($key as $_key=>$_val) {
                    $this->style($_key,$_val);
                }

                return $this;

            } else {

                $style = $this->param("*style");
                return $style[$key];

            }
        }

        if(func_num_args()==2) {

            // Аргумент $val - число, приписываем к нему стандартные единицы измерения
            if(is_numeric($val)) {

                $units = array(
                    "width" => "px",
                    "height" => "px",
                    "margin-left" => "px",
                    "margin-right" => "px",
                    "margin-top" => "px",
                    "margin-bottom" => "px",
                    "left" => "px",
                    "right" => "px",
                    "top" => "px",
                    "bottom" => "px",
                    "padding-left" => "px",
                    "padding-right" => "px",
                    "padding-top" => "px",
                    "padding-bottom" => "px",
                );

                $val = $val.$units[$key];

            }

            $style = $this->param("*style");
            $style[$key] = $val;
            $this->param("*style",$style);
            return $this;
        }

    }

    public final function attr($key,$val=null,$processStyles = true) {

        if(func_num_args()==0) {
            $attributes = $this->param("attributes");
            return $attributes;
        }

        if(func_num_args()==1) {
            $attributes = $this->param("attributes");
            return $attributes[$key];
        }

        if(func_num_args()>=2) {

            if($key=="style" && $processStyles) {
                foreach(explode(";",$val) as $style) {
                    $style = trim($style);
                    list($skey,$sval) = explode(":",$style);
                    $this->style($skey,$sval);
                }
            } else {
                $attributes = $this->param("attributes");
                $attributes[$key] = $val;
                $this->param("attributes",$attributes);
            }
            return $this;
        }

    }

    public function addClass($class) {
        $classes = util::splitAndTrim($this->attr("class")," ");
        $classes[] = $class;
        $classes = array_unique($classes);
        $classes = implode(" ",$classes);
        $this->attr("class",$classes);
        return $this;
    }

    public function name() {
        return "html helper";
    }

    public function execWidget() {

        $style = array();
        foreach($this->param("*style") as $key=>$val) {
            $style[] = $key.":".$val;
        }

        $style = implode(";",$style);

        if($style) {
            $this->attr("style",$style,false);
        }

        if(!$this->param("attributes"))
            $this->param("attributes",array());

        tmp::exec("/tmp/helper/html",$this->params());
    }

}
