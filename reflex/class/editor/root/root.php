<?

/**
 * Модель корневого элемента в каталоге
 **/
class reflex_editor_root extends reflex {

    private static $sessionHashKey = "root-hash";
    private static $sessionDataKey = "root-data";

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
        return reflex_collection::unserialize($this->data("data"));
    }

    public function reflex_cleanup() {
        // Удаляем руты которым больше суток
        if(util::now()->stamp() - $this->pdata("created")->stamp() > 3600*24) return true;
    }

    public function reflex_beforeCreate() {
        $this->data("created",util::now());
    }

    /**
     * Возвращает массив редакторов верхнего уровня
     * Карта парестраивается каждый раз, когда пользователь логинится,
     * выходит или когда его права меняются. Построенная карта сохраняется в сессию
     * (сохраняются id, физически карта хранится в базе). Т.о. все руты всех пользователей
     * хранятся в базе, но видит каждый только свои.
     * + можно скинуть ссылку другому человеку и она сработает
    **/
    public static function level0() {

        $session = mod::service("session");
    
        // Хэш, который меняется при изменении возможностей для просмотра пользолвателя
        $hash = md5(user::active()->data("roles").":".user::active()->id().":".mod_superadmin::check());
        $rebuild = false;
        
        // Если хэш изменился, очищаем кэш
        if($hash!=$session->get(self::$sessionHashKey)) {
			self::clearCache();
        }
			
        $session->set(self::$sessionHashKey,$hash);

        // Достаем сохраненные в сессии данные
        if($session->keyExists(self::$sessionDataKey) && $session->get(self::$sessionDataKey)->value()!=null) {

            $ids = $session->get(self::$sessionDataKey)->value();

        } else {

            $ids = array();
            foreach(self::buildMap() as $item) {
                $ids[] = $item->hash();
            }

            $session->set(self::$sessionDataKey, $ids);

        }

        reflex::storeAll();

        $ret = array();
        foreach($ids as $hash) {
            $ret[] = reflex_editor::byHash($hash);
		}
		return $ret;
        
    }

    public static function clearCache() {
        mod::service("session")->set(self::$sessionDataKey,null);
    }

    /**
     * Строит рут для одной коллекции
     * @return Object class reflex_editor или null
     **/
    private function buildOne($collection) {

		// В зависимости от класса переданного объекта, дейстуем по-разному

		// Из коллекции делаем объект reflex_editor_root
        if(mod::testClass(get_class($collection),"reflex_collection")) {
    
	        if(!$collection->editor()->beforeCollectionView())
	            return false;

	        $group = $collection->param("group");

	        if(!$group)
	            $group = $collection->virtual()->reflex_rootGroup();

	        $root = reflex::create("reflex_editor_root", array(
	            "parent" => 0,
	            "data" => $collection->serialize(),
	            "title" => $collection->title(),
	            "group" => $group,
	            "priority" => $collection->param("priority"),
	            "tab" => $collection->param("tab"),
	        ));

	        return $root->editor();
	        
        }
        
        // Если передан редактор, то кладем в базу его
        if(mod::testClass(get_class($collection),"reflex_editor")) {
            return $collection;
        }
    }
    
    public static function sortRoot($a,$b) {
    
		if($r = $b->rootPriority() - $a->rootPriority()) {
        	return $r;
        }
        
        if($r = strcmp($a->group(),$b->group())) {
            return $r;
        }
            
        return strcmp($a->title(),$b->title());
    
    }
    
    /**
     * Строит карту рутов
     **/
    public static function buildMap() {
    
        $heap = array();
        
        foreach(reflex::classes() as $class) {

            $obj = new $class;
            $items = $obj->reflex_root();
            
            //Если не объект и не масив
            if(!is_object($items) && !is_array($items)) {
                throw new Exception("Метод reflex_root() класса {$class} вернул недопустимое значение");
            }
            
            if(is_object($items)) {
                $items = array($items);
            }
            
            foreach($items as $collection) {
                $editor = self::buildOne($collection);
                if($editor)
                     $heap[] = $editor;
            }

        }
        
        foreach(mod::classes("reflex_editor") as $class) {

            $obj = new $class;
            $items = $obj->root();
            
            //Если не объект и не масив
            if(!is_object($items) && !is_array($items))
                throw new Exception("Метод root() класса {$class} вернул недопустимое значение");

            if(is_object($items))
                $items = array($items);

            foreach($items as $collection) {
                $editor = self::buildOne($collection);
                if($editor)
                     $heap[] = $editor;
            }

        }
        
        usort($heap,array("self","sortRoot"));

        return $heap;
    }

}
