<?

class inx {

	private static $included = false;

	public function inc($name) {
	    self::add();
	    tmp::script("inx.loader.load('$name')");
	}

	public static function add($params=null,$noloader=null) {

		tmp::jq();
		tmp::singleJS("/inx/version/".file::get("/inx/build_id.txt")->data()."/inx.js");

		$ret = "";
		$ret.= "<script>\n";

		if(!self::$included) {
		    $ret.= "inx.conf.url='/inx/version/".file::get("/inx/build_id.txt")->data()."/';\n";
		    foreach(self::$conf as $key => $val) {
		    	$ret.= "inx.conf.$key='$val';\n";
			}
		}

		// Только если мы добавляем компонент
		if($params) {
			if(is_string($params)) $params = array("type"=>$params);
			$params = util::jsonEncode($params);
			$ret.= "inx($params)";
			if(!$noloader) $ret.= ".here()";
			$ret.= "\n";
		}

		$ret.= "</script>\n";
		
		echo $ret;

		self::$included = true;
	}

	private static $conf = array();
	
	public static function conf($key,$val) {
		self::$conf[$key] = $val;
	}

	public static function debug() {
		self::add();
		tmp::js("/inx/pub/inx/debug.js");
	}

}
