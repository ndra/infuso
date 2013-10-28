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
	
    /**
     * Запускает приложение
     **/
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

public static function generateHtaccess() {

	    // Загружаем xml с настройками
	    $htaccess = mod::conf("mod:htaccess");

	    //if(is_array($htaccess)) $htaccess = implode("\n",$htaccess);
	    $htaccess = strtr($htaccess,array('\n'=>"\n"));
		$htaccess.="\n\n";

		if(mod::conf("mod:htaccess-non-www"))
			$htaccess.="
RewriteCond %{HTTPS} off
RewriteCond %{REQUEST_METHOD} !=POST
RewriteCond %{HTTP_HOST} ^www\.(.*)$ [NC]
RewriteRule ^(.*)$ http://%1/$1 [R=301,L]

RewriteCond %{HTTPS} on
RewriteCond %{REQUEST_METHOD} !=POST
RewriteCond %{HTTP_HOST} ^www\.(.*)$ [NC]
RewriteRule ^(.*)$ https://%1/$1 [R=301,L]\n\n
	";

	    // Создаем .htaccess
	    $str = $htaccess;
	    $str.="RewriteEngine on \n";
	    $str.="RewriteCond %{REQUEST_URI} !\. \n";
	    $str.="RewriteRule .* /mod/pub/gate.php [L] \n";

	    foreach(mod::all() as $mod)
	        if($public=mod::info($mod,"mod","public")) {
	            if(!is_array($public)) $public = array($public);
	            foreach($public as $pub) {
	                $pub = $mod."/".trim($pub,"/");
	                $pub = "/".trim($pub,"/")."/";
	                $pub = strtr($pub,array("/"=>'\/'));
	                $str.="RewriteCond %{REQUEST_URI} !^$pub\n";
	            }
			}

		$str.= "RewriteCond %{REQUEST_URI} !\/mod\/pub\/gate.php\n";
		$str.= "RewriteCond %{REQUEST_URI} !^\/?[^/]*$\n";
		$str.= "RewriteRule .* /mod/pub/gate.php [L]\n";
		$str.= "ErrorDocument 404 /mod/pub/gate.php\n";

	    mod_file::get(".htaccess")->put($str);
	}

    /**
     * Один шаг инсталляции приложения
     **/
    public function deployStep($step) {

		if($step==0) {
            $this->generateHtaccess();
		    mod_classmap::buildClassMap();
		    $next = true;
		} else {

            // Метод сортироваки классов
            $sort = function($a,$b) {
                $a = call_user_func(array($a,"priority"));
                $b = call_user_func(array($b,"priority"));
                if($a>$b) {
                    return -1;
                }
                if($a<$b) {
                    return 1;
                }
                return 0;
            };

            $classes = mod::classes("mod_init");
            usort($classes,$sort);
            $class = $classes[$step-1];

            if($class) {
            	$done = false;
            	call_user_func(array($class,"init"));
			} else {
			    $done = true;
			}
		}

        return $done;

    }

    /**
     * Инсталлирует приложение
     **/
    public function deploy() {

        set_time_limit(0);

        $n = 0;
        do {
            $done = mod::app()->deployStep($n);
            $n++;
        } while (!$done);

    }

}
