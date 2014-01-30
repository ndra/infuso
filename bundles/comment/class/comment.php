<?

/**
 * Модель комментария
 **/

class comment extends reflex {

    /**
     * Возвращает коллекцию всех комментариев
     **/
    public static function all() {
        return reflex::get(get_class())->desc("datetime");
    }

    /**
     * @return Возвращает комментарий по id
     **/
    public static function get($data) {
        return reflex::get(get_class(),$data);
    }
    
    public function reflex_root() {
        return array(
            self::all()->title("Все комментарии")->param("tab","system")
        );
    }
    
    public function reflex_beforeCreate() {
        $this->data("userID",user::active()->id());
        $this->data("ip",$_SERVER["REMOTE_ADDR"]);
    }

}
