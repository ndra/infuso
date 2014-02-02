<?

/**
 * Модель корневого элемента в каталоге
 **/
class reflex_editor_root extends reflex {

    public static function all() {
        return reflex::get(get_class());
    }

    public static function get($id) {
        return reflex::get(get_class(),$id);
    }
    
    public function reflex_children() {

        if($this->data("data")) {
            return array($this->getList());
        }
            
        return array();
    }

    public function getList() {
        return \Infuso\ActiveRecord\Collection::unserialize($this->data("data"));
    }

    public function reflex_cleanup() {
        // Удаляем руты которым больше суток
        if(util::now()->stamp() - $this->pdata("created")->stamp() > 3600*24) return true;
    }

    public function reflex_beforeCreate() {
        $this->data("created",util::now());
    }


}
