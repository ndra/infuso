<?

class seo_domainInfo_service extends mod_service {

	public function defaultService() {
	    return "seo/domainInfo";
	}
	
	/**
	 * Функция для зашрузки данных по http
	 * Взята из примера, некайф было разбиратсья чем обычный file_get_contents плох
	 **/
	private static function download($url) {
		$ret = false;
		if( $curl = curl_init() ) {
			if( !curl_setopt($curl,CURLOPT_URL,$url) ) return $ret;
			if( !curl_setopt($curl,CURLOPT_RETURNTRANSFER,true) ) return $ret;
			if( !curl_setopt($curl,CURLOPT_CONNECTTIMEOUT,30) ) return $ret;
			if( !curl_setopt($curl,CURLOPT_HEADER,false) ) return $ret;
			if( !curl_setopt($curl,CURLOPT_ENCODING,"gzip,deflate") ) return $ret;
			$ret = curl_exec($curl);
			curl_close($curl);
		} else {
		    throw new Exception("Failed init curl");
		}
		return $ret;
	}

	/**
	 * Возвращает Yandex CY для домена
	 **/
	function getYandexCY($url){

		if( substr($url,0,7) != 'http://' ) {
			$url = 'http://' . $url;
		}

		if( $content = self::download('http://bar-navig.yandex.ru/u?ver=2&url='. urlencode($url) .'&show=1&post=0') ){

   			if( $xmldoc = new SimpleXMLElement($content) ){
				$tcy = $xmldoc->tcy;
				if( !empty($tcy) ){
					return (integer)$tcy['value'];
				}
			}
		}

		return null;

	}
	
	/**
	 * Возвращает данные о домене $domain
	 * Кэширует результаты на день
	 **/
	public function info($domain,$key=null) {
	
	    $item = seo_domainInfo::all()
			->eq("domain",$domain)
			->eq("date",util::now()->date())
			->one();
			
		if(!$item->exists()) {
		
		    $cy = self::getYandexCY($domain);
		    $item = reflex::create("seo_domainInfo",array(
		        "domain" => $domain,
		        "cy" => $cy,
			));
		
		}
		
		$ret = $item->data();
		
		if($key) {
		    $ret = $ret[$key];
		}
		
		return $ret;
	
	}

}
