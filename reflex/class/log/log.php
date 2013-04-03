<?

/**
 * Модель записи в журнале
 **/ 
class reflex_log extends reflex {

    public static function get($id) {
        return reflex::get(get_class(),$id);
    }

    public static function all() {
        return reflex::get(get_class())->desc("datetime");
    }

    public function reflex_beforeCreate() {
        $this->data("datetime",util::now());
    }

    /**
     * Возвращает пользователя, сделавшего запись
     **/
    public function user() {
        return $this->pdata("user");
    }

    /**
     * Возвращает текст сообщения
     **/
    public function message() {
        return $this->data("text");
    }

    /**
     * Возвращает текст сообщения
     **/
    public function msg() {
		return $this->message();
	}

    /**
     * Иконка для лога
     **/
    public function reflex_icon() {
        return "log";
    }

	/**
	 * Вернет элемент к которому прикреплена запись
	 **/
    public function item() {
        list($class,$id) = explode(":",$this->data("index"));
        return reflex::get($class,$id);
    }

}
