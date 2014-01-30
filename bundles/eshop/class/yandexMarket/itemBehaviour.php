<?

/**
 * Поведение товара для яндекс-маркета
 **/
class eshop_yandexMarket_itemBehaviour extends mod_behaviour {

    public function addToClass() {
       // return mod_conf::get("eshop:yandex:market") ? "eshop_item" : null;
    }
    
    public function behaviourPriority() {
        return -1;
    }
    
    public function fields() {
        return array(
            mod::field("checkbox")->name("yandexMarket")->label("Выгружать в Яндекс.Маркет")->group("Яндекс.Маркет"),
            mod::field("decimal")->name("bid")->label("bid")->group("Яндекс.Маркет"),
            mod::field("decimal")->name("cbid")->label("cbid")->group("Яндекс.Маркет"),
        );
    }
    
    /**
    * Данные для Яндекс.Маркета в формате Вендор.Модель
    **/
    public function yandexMarketData() {
    
        $ret = array();
        $ret["type"] = "vendor.model";
        $ret["available"] = $this->instock() ? "true" : "false";
        
        $domain = mod_url::current()->scheme()."://".mod_url::current()->host();
        $ret["url"] = $domain.$this->url();
        $ret["price"] = $this->price();
        $ret["currencyId"] = "RUR";
        $ret["categoryId"] = $this->group()->id();
    
        if($this->data("photos")) {
            $photo = trim($this->photo()," /");
            $photo = "http://".mod_url::current()->server()."/".$photo;
            $ret["picture"] = $photo;
        }
    
        $ret["vendor"] = $this->vendor()->title();
        // Отклоняем предложения, в которых не указан производитель
        if(!trim($ret["vendor"])) {
            return false;
        }        
        
        $ret["vendorCode"] = $this->data("article");
        $ret["model"] = $this->data("model");
        
        // Отклоняем предложения, в которых не указана модель
        if(!trim($ret["model"])) {
            return false;
        }
        
        // Ставка для поиска
        // Не забываем, что она передается в яндекс в центах, а в админке хранится в долларах
        if($bid = $this->data("bid"))
            $ret["bid"] = $bid*100;
    
        // Ставка для карточки модели
        // Не забываем, что она передается в яндекс в центах, а в админке хранится в долларах
        if($cbid = $this->data("cbid"))
            $ret["cbid"] = $cbid*100;
    
        return $ret;
    }
    
    /**
    * Данные упрощенного описания
    * Приведены для примера, нигде не используются
    **/
    public function exportItemSimply($item) {
        $ret = array();
    
        $domain = mod_url::current()->scheme()."://".mod_url::current()->host();
        $ret["url"] = $domain.$item->url();
        $ret["price"] = $item->price();
        $ret["currencyId"] = "RUR";
        $ret["categoryId"] = $item->group()->id();
    
        $photo = trim($item->photo()," /");
        if($photo) {
            $photo = "http://".mod_url::current()->server()."/".$photo;
            $ret["picture"] = $photo;
        }
    
        // Название товара
        $ret["name"] = $item->title();
    
        $ret["vendor"] = $item->vendor()->title();
        $ret["vendorCode"] = $item->data("article");
    
        return $ret;
    }
    
}
