<?

class seotools_eshop_behaviour extends mod_behaviour {

    public function addToClass() {
        return "eshop_item";
    }
    
    /**
     * Возвращает урл страницы товара на яндекс-маркете
     **/
    public function seotools_yandexMarketURL() {
        $search = $this->vendor()->title()." ".$this->data("model");
        $q = http_build_query(array(
            "text" => $search,
            "cvredirect" => 2,
        ));

        $url = "http://market.yandex.ru/search.xml?".$q;
        return $url;
    }
    
    /**
     * Ставит загрузку данного url в очередь
     **/
    public function seotools_LoadYandexMarketContent() {    
        $url = $this->seotools_yandexMarketURL();
        $headers = "Cookie: yandex_gid=39";        
        $page = seotools_slowLoad::load($url,$headers);
        return $page;
    }
    
    /**
     * Возвращает контент страницы товара на яндекс-маркете (если он уже загружен)
     **/
    public function seotools_getYandexMarketContent() {
        return $this->seotools_LoadYandexMarketContent()->data("content");
    }
    
    public function seotools_prices() {
        //b-top5-offers__list
        $xml = util::str($this->seotools_getYandexMarketContent())->html();
        $top5 = $xml->xpath("//*[@class='b-prices__num']");
        return end($top5)."";
    }

}
