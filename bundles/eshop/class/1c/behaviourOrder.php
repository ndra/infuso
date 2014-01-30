<?

/**
 * Поведение для заказа, содержащее все необходимое для интеграции с 1С
 **/ 
class eshop_1c_behaviourOrder extends mod_behaviour {

   /* public function addToClass() {
        return mod_conf::get("eshop:1c") ? "eshop_order" : null;
    } */
    
    public function behaviourPriority() {
        return -1;
    }
    
    public function fields() {
        return array(
            mod::field("checkbox")->name("1CExportCompleted")->label("Выгружен в 1С")->group("1C"),
        );
    }
    
    /**
    * Медод, генерирующий xml для экспорта заказа
    **/
    public function export1CXML($document) {
    
        $order = $this;
    
        $document->Номер = $order->id();
        $document->Дата = $order->pdata("created")->notime()."";
        $document->ХозОперация = "Заказ товара";
        $document->Роль = "Продавец";
        $document->Валюта = "руб";
        $document->Курс = 1;
        $document->Сумма = $order->total();
        
        // В комментарий записываем полную информацию о заказе
        $document->Комментарий = $this->asText();
    
        // Контрагент
        $kontragenti = $document->addChild("Контрагенты");
        $kontragent = $kontragenti->addChild("Контрагент");
        
        // Переопределите это
        $kontragent->Наименование = "Заказ с сайта";
        $kontragent->Идентификатор = "0000000000";
        
        $kontragent->Роль = "Покупатель";
        $kontragent->ПолноеНаименование = "Заказ с сайта";
    
        // Список товаров
        $tovari = $document->addChild("Товары");
        foreach($order->items() as $item) {
            $tovar = $tovari->addChild("Товар");
            $tovar->Ид = $item->item()->data("importKey");
            $tovar->Наименование = $item->item()->title();
            $tovar->БазоваяЕдиница = "шт";
            $tovar->БазоваяЕдиница->addAttribute("Код",796);
            $tovar->БазоваяЕдиница->addAttribute("НаименованиеПолное","Штука");
            $tovar->БазоваяЕдиница->addAttribute("МеждународноеСокращение","PCE");
            $tovar->ЦенаЗаЕдиницу = $item->price();
            $tovar->Количество = $item->quantity();
            $tovar->Сумма = $item->cost();
        }
    }
    
}
