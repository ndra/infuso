<?

/**
 * Класс для получения инфомрации о странице
 **/
class seo_queryInfo extends mod_service {

	private $source = null;

	public function defaultService() {
	    return "seoQueryInfo";
	}
	
	/**
	 * Устанавливает драйвер
	 * Допустимые значения
	 * google
	 * yandex/39 - Яндекс Ростова-на-Дону (39 - код региона, можно использовать другой код)
	 **/
	public function source($source) {
	    $this->source = $source;
	    return $this;
	}

	/**
	 * Возвращает информацию о странице
	 **/
	public function info($url) {
	
	    if($this->source == "google") {
	        return seo_queryInfo_google::get($url);
	    }
	    
	    throw new Exception("seo_queryInfo:info() вad source [{$this->source}]");

	}

}
