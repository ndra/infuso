<?

class inx {

	private static $included = false;
	
	public function inc($name) {
	    self::add();
	    tmp::script("inx.loader.load('$name')");
	}

	public static function add($params=null,$noloader=null) {
	
	    $version = 777;
	
	    $bundlePath = mod::service("classmap")->getClassBundle(get_class())->path();

		tmp::jq();
		tmp::singleJS($bundlePath."/version/{$version}/inx.js");

		$ret = "";
		$ret.= "<script>\n";

		if(!self::$included) {
		    $ret.= "inx.conf.boardRes='/bundles/board/res/';\n";
		    $ret.= "inx.conf.url='{$bundlePath}/version/".$version."/';\n";
		    $ret.= "inx.conf.res='{$bundlePath}/res/';\n";
		}

		// Только если мы добавляем компонент
		if($params) {
		
			if(is_string($params)) {
				$params = array (
					"type" => $params
				);
			}
				
			$params = util::jsonEncode($params);
			$ret.= "inx($params)";
			
			if(!$noloader) {
				$ret.= ".here()";
			}

			$ret.= "\n";
		}

		$ret.= "</script>\n";
		
		echo $ret;

		self::$included = true;
	}

	/**
	 * Включает режим отлажки inx (подключает специальный скрипт)
	 **/
	public static function debug() {
		self::add();
		tmp::js("/inx/pub/inx/debug.js");
	}

}
