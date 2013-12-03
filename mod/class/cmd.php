<?

namespace infuso\core;

class cmd extends controller {

	public function indexTest() {
	    return true;
	}

	public function index_404() {
	
		header("HTTP/1.0 404 Not Found");
        $tmp = mod::conf("mod:404");
        if(!$tmp)
			$tmp = "mod:404";
		if(mod::service("classmap")->testClass("tmp"))
        	tmp::exec($tmp);
		else
		    echo "404: Рage not found";

	}
	
	public function index_exception($p) {
	    tmp::exec("/mod/exception",$p);
	}

	// Вызывает ошибку http и завершает работу скрипта
	public static function error($code) {
	
	    if($code==404) {
			mod::action("mod_cmd",404)->exec();
		}
		echo "<!--".str_repeat("**** ",100)."-->";
		die();
	}
	
	public static function handleError() {
	
	
	    # Getting last error
	    $error = error_get_last();
	    
	    # Checking if last error is a fatal error
	    if(!in_array($error["type"],array(E_WARNING,E_NOTICE))) {
			 throw new Exception($error["message"]);
	    }
	    
	}

}
