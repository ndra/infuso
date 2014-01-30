<?

/**
 * Граббер позиций с яндекса
 **/
class eshop_yandexMarket_bidGrabber extends mod_component implements mod_handler {

    public function on_mod_cron() {
        if(!mod_conf::get("eshop:yandex:market:grab-bids"))
            return;
        self::grab();
    }

    public function grab() {

        $items = eshop_item::all()->eq("yandexMarket",1)->lt("bidGrabbed",util::now()->shift(-60*60*4))->orr()->where("`bidGrabbed` is null")->asc("id")->limit(100);
        if(!$items->one()->id())
            return;

        $url = "https://api.partner.market.yandex.ru/v1/campaigns/2029765/bids/recommended.json";
        $data = array(
            "offers" => array(),
        );

        foreach($items as $item) {
            $ydata = $item->yandexMarketData();
            $data["offers"][] = $ydata["typePrefix"]." ".$ydata["vendor"]." ".$ydata["model"];
        }

        $data = eshop_yandexMarket_api::call($url,$data);

        foreach($items as $key=>$item) {
            $ydata = $data["recommendations"][$key];
            $itemData = array(
                "bid1" => $ydata["modelCard"]["posRecommendations"][1]["cbid"]*1,
                "bid2" => $ydata["modelCard"]["posRecommendations"][2]["cbid"]*1,
                "bid3" => $ydata["modelCard"]["posRecommendations"][3]["cbid"]*1,
                "bid4" => $ydata["modelCard"]["posRecommendations"][4]["cbid"]*1,
                "bid5" => $ydata["modelCard"]["posRecommendations"][5]["cbid"]*1,
                "bidGrabbed" => util::now(),
            );
            foreach($itemData as $name=>$val)
                $item->data($name,$val);

        }

    }

}
