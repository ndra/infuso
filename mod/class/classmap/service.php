<?

class mod_classmap_cervice extends mod_service {

	private static $extends = array();

	public function defaultService() {
	    return "classmap";
	}
	
	/**
	 * @return Возвращает список всех классов
	 * @return Если указан параетр extends, возвращает список всех классов, расширяющих extends
	 **/
	public static function getClassesExtends($extends=null) {

		$ret = mod::classmap();
		$ret = $ret["map"];

		if(!$ret) {
		    $ret = array();
		}

		if($extends) {

		    if(!array_key_exists($extends,self::$extends)) {
				self::$extends[$extends] = array();
		        foreach($ret as $key=>$classProos) {
		            if(in_array($extends,$classProos["p"]) && !$classProos["a"]) {
		                self::$extends[$extends][] = $key;
					}
				}
		    }

		    return self::$extends[$extends];

		}

		return $ret;
	}

}
