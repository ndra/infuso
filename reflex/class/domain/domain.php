<?

/**
 * Модель домена модуля reflex
 **/
class reflex_domain extends reflex{

	private static $active = null;

	public static function all() {
		return reflex::get(get_class())->asc("priority")->param("sort",true);
	}
	
	public static function get($id) {
		return reflex::get(get_class(),$id);
	}

	public function reflex_title() {
	
	    if(!$this->exists())
	        return "";
	
		$ret = trim($this->data("title"));
		if(!$ret)
		    $ret = $this->firstDomain();
		if(!$ret)
		    $ret = "Домен:".$this->id();
		return $ret;
	}

	public function active() {
		if(!self::$active) {
		    self::$active = self::get(0);
			$url = mod_url::current()->domain();
			foreach(self::all() as $domain) {
			    foreach($domain->domainList() as $d)
			    	if(trim($d)==$url) {
						self::$active = $domain;
						break;
					}
			}
		}
		return self::$active;
	}

	public static function reflex_root() {
		return array(
		    self::all()->title("Домены")->param("tab","system"),
		);
	}

	public function domainList() {
		return util::splitAndTrim($this->data("domains"),"\n");
	}

	public function firstDomain() {
		$ret = $this->domainList();
		return $ret[0];
	}

	public function reflex_url() {
		$domains = $this->domainList();
		return "http://".$domains[0];
	}
	
}
