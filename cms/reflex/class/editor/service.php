<?

class reflex_editor_service extends mod_service {

    private static $sessionHashKey = "root-hash";
    private static $sessionDataKey = "root-data";

    public function defaultService() {
        return "reflexEditor";
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

        // Если хэш изменился, очищаем кэш
        if($hash != $session->get(self::$sessionHashKey)) {
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

    /**
     * Строит карту рутов
     **/
    public static function buildMap() {

        mod_profiler::beginOperation("reflex","buildMap",1);

        $ritems = array();

        foreach(reflex::classes() as $class) {
            $obj = new $class;
            $ritems[] = $obj->reflex_root();
        }

        foreach(mod::service("classmap")->map("reflex_editor") as $class) {
            $obj = new $class;
            $ritems[] = $obj->root();
        }

        $heap = array();
        
        foreach($ritems as $items) {

            //Если не объект и не масив
            if(!is_object($items) && !is_array($items)) {
                throw new Exception("Метод reflex_root() вернул недопустимое значение");
            }

            if(is_object($items)) {
                $items = array($items);
            }

            foreach($items as $collection) {
                $editor = self::buildOne($collection);
                if($editor) {
                     $heap[] = $editor;
                }
            }

        }

        usort($heap,array("self","sortRoot"));

        mod_profiler::endOperation();

        return $heap;
    }

    /**
     * Строит рут для одной коллекции
     * @return Object class reflex_editor или null
     **/
    private function buildOne($collection) {
    
        mod_profiler::beginOperation("reflex","buildOne",1);

		// В зависимости от класса переданного объекта, дейстуем по-разному

		// Из коллекции делаем объект reflex_editor_root
        if(mod::service("classmap")->testClass(get_class($collection),"infuso\ActiveRecord\Collection")) {

            if(!$collection->editor()->beforeCollectionView()) {
                mod_profiler::endOperation();
	            return false;
            }

	        $group = $collection->param("group");

	        if(!$group) {
	            $group = $collection->virtual()->reflex_rootGroup();
            }
            
            $root = reflex::create("reflex_editor_root", array(
	            "parent" => 0,
	            "data" => $collection->serialize(),
	            "title" => $collection->title(),
	            "group" => $group,
	            "priority" => $collection->param("priority"),
	            "tab" => $collection->param("tab"),
	        ));

            mod_profiler::endOperation();
	        return $root->editor();

        }

        // Если передан редактор, кладем в базу его
        if(mod::service("classmap")->testClass(get_class($collection),"reflex_editor")) {
            return $collection;
        }
    }

    public static function clearCache() {
        mod::service("session")->set(self::$sessionDataKey,null);
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


}
