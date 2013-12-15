<?

/**
 * Класс для получения инфомрации о странице
 **/
class seo_pageInfo extends mod_service {

	public function defaultService() {
	    return "seoPageInfo";
	}

	/**
	 * Возвращает информацию о странице
	 **/
	public function info($url) {
	
	    $info = array();

		if( $curl = curl_init() ) {
			if( !curl_setopt($curl,CURLOPT_URL,$url) ) return $ret;
			if( !curl_setopt($curl,CURLOPT_RETURNTRANSFER,true) ) return $ret;
			if( !curl_setopt($curl,CURLOPT_CONNECTTIMEOUT,30) ) return $ret;
			if( !curl_setopt($curl,CURLOPT_HEADER,false) ) return $ret;
			if( !curl_setopt($curl,CURLOPT_ENCODING,"gzip,deflate") ) return $ret;
			$html = curl_exec($curl);
			$curlInfo = curl_getinfo($curl);
			curl_close($curl);
		} else {
		    throw new Exception("Failed init curl");
		}
		
		$info["httpCode"] = $curlInfo["http_code"];
		
		if($info["httpCode"] != 200 ) {
		    $info["errors"][] = "Сервер вернул код {$info[httpCode]}";
		    return $info;
		}

        $html = util::str($html)->html();
        
	    $info["title"] = (string)end($html->xpath("//title"));
	    $info["h1"] = (string)end($html->xpath("//h1"));
	    
	    // Находим ошибки на странице
		// Отсутствие h1
	    if(!$info["h1"]) {
	        $info["errors"][] = "Не найден h1";
	    }
	    
	    // Несколько заголовков h1
	    $h1count = sizeof($html->xpath("//h1"));
	    if($h1count > 1) {
	        $info["errors"][] = "Несколько заголовков h1 ({$h1count})";
	    }
	    
	    // Отсутствие title
	    if(!$info["title"]) {
	        $info["errors"][] = "Не найден title";
	    }
	    
	    return $info;
	}

}
