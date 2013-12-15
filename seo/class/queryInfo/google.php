<?

/**
 * Драйвер снятия позиций для google
 **/
class seo_queryInfo_google {

	/**
	 * Возвращает инфомрацию о запросе
	 **/
    public static function get($query) {

        $q = urlencode($query);
        $file = file::http("http://www.google.ru/search?sourceid=chrome&ie=UTF-8&q={$q}&num=100");
        $str = $file->data();
        $str = util::str($str)->decode();
        $ret = array();
        
        if($str) {

            $xml = util::str($str)->html();

            foreach($xml->xpath("//li[@class='g']") as $item) {
            
                $url = (string) end($item->xpath("descendant::cite"));
                $snippet = (string) end($item->xpath("descendant::span[@class='st']"));

                $ret[] = array(
                    "url" => $url,
                    "snippet" => $snippet,
				);
            }
        }
        
        return $ret;
        
    }

}
