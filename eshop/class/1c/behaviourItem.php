<?

/**
 * Поведение для товарной позиции, содержащее все необходимое для интеграции с 1С
 **/ 
class eshop_1c_behaviourItem extends mod_behaviour {

	/**
	 * @todo add conf here
	 **/
    public function addToClass() {
//        return mod_conf::get("eshop:1c") ? "eshop_item" : null;
    }

    public function behaviourPriority() {
        return -1;
    }

    /**
     * Дополнительные поля для управления импортом
     **/
    public function fields() {
        return array(
            mod_field::get("textfield")->name("importKey")->disable()->label("1С: Внешний ключ")->group("1C"),
            mod_field::get("datetime")->name("importTime")->disable()->label("1С: Время испорта")->group("1C"),
            mod_field::get("textfield")->name("importCycle")->disable()->label("1С: Цикл импорта")->group("1C"),
            mod_field::get("checkbox")->name("skipImport")->label("1С: Не менять данные товара при импорте")->group("1C"),
            mod_field::get("checkbox")->name("skipImportSys")->disable()->group("1C"),
        );
    }

    /**
     * Будет ли товар пропущен при импорте
     **/
    public function skipImport() {
        return $this->data("skipImportSys");
    }

    /**
     * Ремонт элемента
     **/
    public function reflex_repairSys() {

        // Будет ли товар пропущен при импорте
        $skip = false;
        foreach($this->groups() as $group)
            if($group->data("skipImportChildren")) {
                $skip = true;
                break;
            }
        if($this->data("skipImport"))
            $skip = true;
        $this->data("skipImportSys",$skip);

    }

    /**
     * Метод, обрабатывающий товар из файлв import.xml
     * В этом файле приходят описания товаров и групп
     **/
    public function processCatalogXML($towar,$xml) {

        $importKey = $towar->Ид."";
        $data = array(
            "title" => $towar->Наименование."",
            "importKey" => $importKey,
            "article" => $towar->Артикул."",
            "photos" => $photos,
            "description" => $towar->Описание."",
            "order" => true,
        );
        
        // Загружаем группу
        $groupID = $towar->Группы->Ид."";
        $groupXML = end($xml->xpath("//Классификатор/Группы/descendant::Группа[Ид='$groupID']"));
        $vgroup = reflex::virtual("eshop_group");
        $group = $vgroup->processCatalogXML($groupXML,$xml);
        $data["parent"] = $group->id();        
        
        $item = eshop_1c_utils::importItem($data);
    }

    /**
     * Метод, обрабатывающий товар из файла offers.xml
     * В этом файле приходит информация о ценах на товарные позиции
     * (сами товары загружаются в import.xml)
     **/
    public function processOffersXML($offer,$xml) {
        $data = array(
            "price" => end($offer->xpath("descendant::ЦенаЗаЕдиницу"))."",
            "instock" => $offer->Количество."",
            "importKey" => $offer->Ид."",
        );
        eshop_1c_utils::importItem($data);
    }

}
