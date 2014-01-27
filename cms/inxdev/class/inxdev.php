<?

/**
 * Контроллер примеров inx
 **/ 
class inxdev extends mod_controller {

    private $path;

	public static function indexTest() {
		return true;
	}
	
	public static function index($p) {
	
		if($p["page"]) {
		    $page = self::get($p["page"]);
		    tmp::add("center","/inxdev/page",$page);
	    } else {
	    	tmp::add("center","/inxdev/index");
	    }
	    tmp::exec("/inxdev/layout");
	}

	public static function get($path) {
	    return new inxdev($path);
	}

	public function inc() {
	    $bundlePath = self::inspector()->bundle()->path();
	    $php = "/{$bundlePath}/doc/".$this->path()."/index.php";
	    file::get($php)->inc();
	}

	public function __construct($path=null) {
	    $this->path = file::get($path)->path();
	}

	public function xml() {
	    $ret = simplexml_load_string(file::get("/inxdev/doc/{$this->path()}/index.xml")->data());
	    return $ret;
	}

	/**
	 * Возвращает потомков текущей страницы
	 **/	 	
	public function children() {
	
		$bundlePath = self::inspector()->bundle()->path();
	
	    $ret = array();
	    foreach(file::get("/{$bundlePath}/doc/".$this->path())->dir()->folders() as $sub)
			$ret[] = self::get($this->path."/".$sub->name());
	    return $ret;
	}

	public function title() {
	    return $this->path();
	}

	public function path() {
	    return $this->path;
	}

	public function url() {
		return "/inxdev/example".file::get($this->path())->path();
	}  

}
