<?

/**
 * Поведение для группы тоаров, содержащее все необходимое для интеграции с 1С
 **/ 
class eshop_1c_behaviourGroup extends mod_behaviour {

	/**
	 * @todo add conf here
	 **/
    public function addToClass() {
        // return mod_conf::get("eshop:1c") ? "eshop_group" : null;
    }
    
    public function fields() {
        return array(
            mod_field::get("textfield")->name("importKey")->disable()->label("1С: Внешний ключ")->group("1C"),
            mod_field::get("datetime")->name("importTime")->disable()->label("1С: Время испорта")->group("1C"),
            mod_field::get("textfield")->name("importCycle")->disable()->label("1С: Цикл импорта")->group("1C"),
            mod_field::get("checkbox")->name("skipImport")->label("1С: Не менять данные товара при импорте")->group("1C"),
            mod_field::get("checkbox")->name("skipImportSys")->disable()->group("1C"),
            mod_field::get("checkbox")->name("skipImportChildren")->label("1С: Не менять содержимое при импорте")->group("1C"),
        );
    }
    
    /**
    * Будет ли товар пропущен при импорте
    **/
    public function skipImport() {
        return $this->data("skipImportSys");
    }
    
    public function reflex_repairSys() {
    
        // Будет ли группа пропущена при импорте
        $skip = false;
        foreach($this->parents() as $group)
            if($group->data("skipImportChildren")) {
                $skip = true;
                break;
            }
        if($this->data("skipImport")) $skip = true;
        $this->data("skipImportSys",$skip);
    
    }
    
    public function processCatalogXML($groupXML,$xml) {
    
        $group = array(
            "title" => $groupXML->Наименование."",
            "importKey" => $groupXML->Ид."",
        );
        
        $parentXML = end($groupXML->xpath("parent::Группы/parent::Группа"));
        if($parentXML) {
            $parent = $this->processCatalogXML($parentXML,$xml);
            $group["parent"] = $parent->id();
        }
        
        return eshop_1c_utils::importGroup($group);
        
    }

}
