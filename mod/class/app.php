<?

class mod_app {

	private $post;
	private $get;
	private $files;
	
	private static $current;
	
	public function current() {
	
	    if(!self::$current) {
	    
		    $p = $_SERVER["REQUEST_URI"];
	        $server = $_SERVER["SERVER_NAME"];
	        $url = mod_url::get("http://$server$p");

		    self::$current = new self(array(
		        "url" => $url,
		        "post" => $_POST,
		        "files" => $_FILES,
			));
	    
	    }
	    
	    return self::$current;

	}
	
	public function __construct($params) {
	    $this->url = $params["url"];
	    $this->post = $params["post"];
	    $this->files = $params["files"];
	}

	/**
	 * Возвращаем массив $_POST
	 **/
	public function url() {
	    return $this->url;
	}
	
	/**
	 * Возвращаем массив $_POST
	 **/
	public function post() {
	    return $this->post;
	}
	
	public function files() {
		return $this->files;
	}
	
	public function action() {
	
	    if(!$this->action) {
	        $this->action = $this->url()->action();
	    }
	    return $this->action;
	}
	
	/**
	 * Возвращает текущую записаь active record (reflex)
	 **/
	public function ar() {
	    return $this->ar;
	}
	
	public function exec() {
	
	    Header("HTTP/1.0 200 OK");
	
		try {

			// Выполняем post-команду
		    mod_post::process($this->post(),$this->files());

		    // Выполняем экшн
		    $action = $this->action();
		    
		    if($action) {
		        $action->exec();
		    } else {
		        mod_cmd::error(404);
		    }

		} catch(Exception $exception) {

		    while(ob_get_level()) {
		        ob_end_clean();
		    }

		    // Трейсим ошибки
		    mod::trace($_SERVER["REMOTE_ADDR"]." at ".$_SERVER["REQUEST_URI"]." got exception: ".$exception->getMessage());

		    try {

		        tmp::destroyConveyors();

		        $action = mod::action("mod_cmd","exception")
		            ->param("exception",$exception)
		            ->exec();

		    } catch(Exception $ex2) {
		        throw $exception;
		    }

		}
	
	}

}
