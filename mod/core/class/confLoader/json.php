<?

/**
 * Класс для загрузки xml конфигурации
 **/
class mod_confLoader_json {

	/**
	 * json => php array
	 **/
	public static function read($doc) {
		return json_decode($doc);
	}

	/**
	 * php array() => json
	 **/
	public static function write($data) {
	
	    $data = self::prepareArray($data);
	
		return json_encode($data);
	}
	
	public static function prepareArray($data) {
	
	    if(is_scalar($data)) {
	        return $data;
	    } elseif (is_array($data)) {
	    
	        foreach($data as $key=>$val) {
	            $data[$key] = self::prepareArray($val);
	        }
	        
	        return $data;
	    
	    } elseif (is_object($data)) {
	    
			if(method_exists($data,"__toString")) {
			    return $data->__toString();
			}
	    
	        return null;
	    }

	}
	
}
