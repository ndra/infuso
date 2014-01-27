<?

/**
 * Набор утилит для импорта товаров
 **/ 
class eshop_1c_utils extends mod_component {

    private static $importCycle;

    /**
     * Импортирует товар
     * Создает товар, если товар не существует
     * Если товар существует, обновляет его данные
     **/
    public static function importItem($p) {
    
        if(!isset($p["active"])) {
            $p["active"] = 1;
        }
            
        $p["importCycle"] = self::importCycle();
        $item = eshop_item::allEvenHidden()->eq("importKey",$p["importKey"])->one();
        
        if($item->skipImport()) {
            return;
        }
            
        if(!$item->exists()) {
            $item = reflex::create("eshop_item",$p);
            if(!isset($p["order"])) $p["order"] = true;
        }
        
        foreach($p as $key => $val) {
            $item->data($key,$val);
        }
            
        $item->data("importTime",util::now());
        return $item;
    }

    /**
     * Импортирует группу товаров
     * Создает группу товаров, если группа не существует
     * Если группа существует, обновляет ее данные
     **/
    public static function importGroup($p) {
    
        if(!isset($p["active"])) {
            $p["active"] = 1;
        }
            
        $p["importCycle"] = self::importCycle();
        $group = eshop_group::allEvenHidden()->eq("importKey",$p["importKey"])->one();
        
        if($group->skipImport()) {
            return;
        }
            
        if(!$group->exists()) {
            $group = reflex::create("eshop_group",$p);
        }
            
        foreach($p as $key=>$val) {
            $group->data($key,$val);
        }
            
        return $group;
    }

    /**
     * Импортирует вендора
     **/
    public static function importVendor($p) {
    
        if(!isset($p["active"])) {
            $p["active"] = 1;
        }
            
        $vendor = eshop_vendor::allEvenHidden()->eq("importKey",$p["importKey"])->one();
        $p["importCycle"] = self::importCycle();
        
        if(!$vendor->exists()) {
            $vendor = reflex::create("eshop_vendor",$p);
        }

        foreach($p as $key=>$val) {
            $vendor->data($key,$val);
        }
            
        return $vendor;
    }

    /**
     * Начинает цикл импорта
     * Используется через роутер
     **/
    private static function newImportCycle() {
        file::mkdir("/eshop/system/");
        file::get("/eshop/system/import.txt")->put(util::id());
    }

    /**
     * Начинает цикл импорта
     **/
    public static function importBegin() {
        self::newImportCycle();
    }

    /**
     * @return Возвращает идентицикатор цикла импорта - уникальную строку
     **/
    public static function importCycle() {
        if(!self::$importCycle) {
            self::$importCycle = file::get("/eshop/system/import.txt")->data();
        }
        return self::$importCycle;
    }

    /**
     * Завершает цикл импорта
     * Скрывает все элементы, которые не затронул импорт
     **/
    public static function importComplete() {
        reflex::storeAll();
        
        // Прячем все товары, которые не были импортированы
        eshop_item::allEvenHidden()->neq("importCycle",self::importCycle())->neq("skipImportSys",1)->data("active",0)->data("activeSys",0);
        
        // Прячем все группы, которые не были импортированы
        eshop_group::allEvenHidden()->neq("importCycle",self::importCycle())->neq("skipImportSys",1)->data("active",0);
        
        // Прячем всех вендоров, которые не были импортированы
        eshop_vendor::allEvenHidden()->neq("importCycle",self::importCycle())->data("active",0);
    }

}
