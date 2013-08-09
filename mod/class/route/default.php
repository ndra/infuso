<?

class mod_route_default extends mod_route {

	public function priority() {
		return -1000;
	}

	public function forward($url) {
	
		$p = explode("/",trim($url->path(),"/"));
		$class = array_shift($p);
		$action = array_shift($p);
		$params = $_GET;

		foreach($p as $key=>$val) {
		    if($key%2==0) {
		        $k = $val;
		    } else {
		        $params[$k] = $val;
		    }
		}

		if(mod::testClass($class,"mod_controller")) {
			return mod_action::get($class,$action,$params);
		}
	}

	public function backward($controller) {
		$ret = "/".$controller->className()."/".$controller->action()."/";
		foreach($controller->params() as $key=>$val) {
		    $ret.= "$key/$val/";
		}
		return $ret;
	}

}
