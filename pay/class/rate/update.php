<?

class pay_rate_update implements mod_handler {
    
    public function on_mod_cron() {
    
        $today = date("d/m/Y");
        $xml = simplexml_load_file("http://www.cbr.ru/scripts/XML_daily.asp?date_req=$today");
        
        if(!$xml)
            return;
            
        $data = array();
        foreach($xml->xpath("*") as $item) {
            $data[$item->NumCode.""] = strtr($item->Value."",array(","=>"."))*1;
        }        
        
        foreach($data as $code=>$rate) {
        
            // Юани считаются десятками, приводим к единому формату
            if($code==156) {
                $rate/=10;
            }
        
            pay_rate::setRate(643,$code,$rate);
            pay_rate::setRate($code,643,1/$rate);
        }
        
        reflex::storeAll();
        mod::fire("pay_ratesUpdated");            
    }
    
}