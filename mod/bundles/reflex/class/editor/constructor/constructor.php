<?

/**
 * Модель конструктора
 * Конструктор - это специальный элемент, который создается пр нажатии кнопки "+"
 * Форма создания элемента - это фактически форма редактирования конструктора  
 **/ 
class reflex_editor_constructor extends reflex {

	public $exists;

    public static function all() {
        return reflex::get(get_class())->desc("created");
    }
    
    public static function get($data) {
        return reflex::get(get_class(),$data);
    }
    
    public function reflex_cleanup() {
        // Удаляем конструкторы которым больше суток
        if(util::now()->stamp() - $this->pdata("created")->stamp() > 3600*24) return true;
    }
    
    public function reflex_beforeCreate() {
        $this->data("created",util::now());
        $this->data("userID",user::active()->id());
    }
    
    public function getList() {
        return reflex_collection::unserialize($this->data("listData"));
    }
    
    public function reflex_title() {
        return "Новый объект ".get_class($this->getList()->one());
    }
    
    public function reflex_parent() {
        return $this->getList()->one()->parent();
    }
    
}
