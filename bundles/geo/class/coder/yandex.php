<?

/**
 * Класс-обертка для доступа к геокодеру яндекса
 **/
class geo_coder_yandex {

	public static function getCoords($txt) {
	
		$key = "gro:".$txt;
		
		// Пытаемся достать точку из сессии
		$point = mod_cache::get($key);
		if($point) {
		    return mod::field("point")->value($point);
		}
		    
		$url = "http://geocode-maps.yandex.ru/1.x/";
		$url.= "?".http_build_query(array(
		    "geocode" => $txt,
		    "format" => "json",
		));
		
		$data = @file_get_contents($url);
		$data = json_decode($data,1);
		$point = $data["response"]["GeoObjectCollection"]["featureMember"][0]["GeoObject"]["Point"]["pos"];
		$point = strtr($point," ",",");
		$point = mod::field("point")->value($point);
		
		mod_cache::set($key,$point->value());
		
		return $point;
	
	}

}
