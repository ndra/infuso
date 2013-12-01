<?

class mod_app {

	private $post;
	private $get;
	private $files;

	private static $initiated = false;
	
	/**
	 * Процессор шаблонов приложения
	 **/
	private $templateProcessor = null;
	
	/**
	 * Текущий экземпляр объекта приложения
	 **/
	private static $current;
	
	/**
	 * Возвращает текущий экземпляр объекта приложерия
	 **/
	public function current() {
	
	    if(!self::$current) {
	    
		    $p = $_SERVER["REQUEST_URI"];
	        $server = $_SERVER["SERVER_NAME"];
	        $url = "http://{$server}{$p}";

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
	    $this->init();
	}
	
	/**
	 * Подключает жизненно важные классы
	 **/
	public function includeCoreClasses() {
	    include("component.php");
	    include("controller/controller.php");
	    include("profiler.php");
	    include("superadmin.php");
	    include("mod.php");
	    include("service.php");
	    include("classmap/service.php");
	    include("file/file.php");
	    include("file/filesystem.php");
	    include("file/list.php");
	}
	
	public function setErrorLevel() {
		error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);
		ini_set("display_errors",1);
	}
	
	public function configureIni() {
		ini_set('register_globals', 'off');
		ini_set('magic_quotes_gpc', 'off');
		ini_set('magic_quotes_runtime', 'off');
		ini_set('default_charset', "utf-8");
	}
	
	/**
	 * Коллбэк для загрузки несуществующего класса
	 **/
	public function loadClass($class) {
		$this->service("classmap")->includeClass($class);
	}
	
	public function init() {
	
	    if(!self::$initiated) {

		    self::$initiated = true;

   			$this->configureIni();
   			$this->setErrorLevel();
			$this->includeCoreClasses();

			spl_autoload_register(array($this,"loadClass"));
		}
		
		$this->registerService("classmap","mod_classmap_service");
		$this->registerService("route","mod_route_service");
		$this->registerService("bundle","mod_bundle_manager");
		$this->registerService("yaml","mod_confLoader_yaml");
		
	}

	/**
	 * Возвращаем массив $_POST
	 **/
	public function url() {
	    return mod_url::get($this->url);
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
	 * Возвращает текущую запись active record (reflex)
	 **/
	public function ar() {
	    return $this->ar;
	}
	
	public function tmp() {
	    if(!$this->templateProcessor) {
	        $this->templateProcessor = new tmp_processor();
	    }
	    return $this->templateProcessor;
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

		        /*$action = mod::action("mod_cmd","exception")
		            ->param("exception",$exception)
		            ->exec(); */
		            
				die($exception);

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

		$bundleManager = mod::service("bundle");
	    foreach($bundleManager->all() as $bundle) {
            foreach($bundle->publicFolders() as $pub) {
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
		    mod_classmap_builder::buildClassMap();
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

            $classes = mod::service("classmap")->classes("mod_init");
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
    
    private $registerdServices = array();
    
    /**
     * Возвращает службу (объект) по имени службы
     **/
    public function service($name) {
    
        $class = $this->registredServices[$name];
        
        if(!$class) {
            $services = $this->service("classmap")->classmap("services");
            $class = $services[$name];
        }
        
        if(!$class) {
            throw new Exception("Service [$name] not found");
        }
        
        return $class::serviceFactory();
    
        /**$class = mod_conf::general("services",$name,"class");





        
        **/
    }
    
    public function registerService($service,$class) {
        $this->registredServices[$service] = $class;
    }

}
