<?

namespace infuso\core;

class app {

	private $post;
	private $get;
	private $files;

	private static $initiated = false;
	
	/**
	 * Список зарегистрирвоанных служб
	 **/
	private $registerdServices = array();
	
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
	    return self::$current;
	}
	
	public function __construct($params) {
	    $this->url = $params["url"];
	    $this->post = $params["post"];
	    $this->files = $params["files"];
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
	    include("file/localFile.php");
	    include("file/flist.php");
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

			// Регистрируем загрузчик классов
			spl_autoload_register(array($this,"loadClass"));
		}
		
		$this->registerService("classmap","infuso\\core\\classmapService");
		$this->registerService("route","\\infuso\\core\\route\\service");
		$this->registerService("bundle","\\infuso\\core\\bundle\\manager");
		$this->registerService("yaml","mod_confLoader_yaml");
		$this->registerService("cache","\\infuso\\core\\cache\\service");
	}

	/**
	 * Возвращает объект текущего урл
	 **/
	public function url() {
	    return url::get($this->url);
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
	        $this->templateProcessor = new \tmp_processor();
	    }
	    return $this->templateProcessor;
	}
	
    /**
     * Запускает приложение
     **/
	public function exec() {
	
	    self::$current = $this;
	    $this->init();
	
	    Header("HTTP/1.0 200 OK");

		try {

			// Выполняем post-команду
		    post::process($this->post(),$this->files());

		    // Выполняем экшн
		    $action = $this->action();

		    if($action) {
		        $action->exec();
		    } else {
		        cmd::error(404);
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

	    file::get(".htaccess")->put($str);
	}

    /**
     * Один шаг инсталляции приложения
     **/
    public function deployStep($step) {

		if($step==0) {
            $this->generateHtaccess();
		    classmap\builder::buildClassMap();
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
    
    /**
     * Возвращает службу (объект) по имени службы
     * @todo вернуть назначение класса службам через конфиг
     **/
    public function service($name) {
    
        $class = $this->registredServices[$name];
        
        if(!$class) {
            $services = $this->service("classmap")->classmap("services");
            $class = $services[$name];
        }
        
        if(!$class) {
            throw new \Exception("Service [$name] not found");
        }
        
        return $class::serviceFactory();
    
        /**$class = mod_conf::general("services",$name,"class");
        **/
    }
    
    public function registerService($service,$class) {
        $this->registredServices[$service] = $class;
    }

}
