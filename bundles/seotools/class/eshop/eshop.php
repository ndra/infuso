<?

class seotools_eshop {

    public function createYandexMarketLoadTask() {
        
        reflex_task::add("eshop_item","`activeSys`=1","seotools_LoadYandexMarketContent");
        
    }

}