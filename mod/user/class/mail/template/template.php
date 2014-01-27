<?

/**
 * Модель шаблона письма
 **/
class user_mail_template extends reflex {


    /**
     * Возвращает коллекцию всех элементов
     **/
    public static function all() {
        return reflex::get(get_class())->asc("code");
    }

    /**
     * @return Возвращает элемент по id
     **/
    public static function get($data) {
        return reflex::get(get_class(),$data);
    }
    
    public function reflex_root() {
        return array(
            self::all()->param("tab","user")->title("Шаблоны писем"),
        );
    }
    
    public function reflex_title() {
        return $this->data("code");
    }
    
    public function dataWrappers() {
        return array(
            "disable" => "mixed/data",
            "code" => "mixed/data",
            "enable" => "mixed/data",
            "subject" => "mixed/data",
        );
    }
    
    
}
