<?

namespace infuso\core\bundle;
use infuso\core\file as file;
use infuso\core\mod as mod;

/**
 * Класс, реализующий бандл
 **/
class bundle extends \infuso\core\component {

	private $path = null;
	
	public function __construct($path) {
		$this->path = $path;
	}

	public function path() {
	    return (string) $this->path;
	}

	/**
	 * Возвращает массив конфигурации бандла
	 **/
	public function conf() {
	
	    $file = file::get($this->path()."/.infuso");
	    if($file->exists()) {
		    $data = file::get($this->path()."/.infuso")->data();
			$conf = mod::service("yaml")->read($data);
		} else {
		    $conf = file::get($this->path()."/info.ini")->ini(true);
			$conf["public"] = $conf["mod"]["public"];
			$conf["leave"] = $conf["mod"]["leave"];
		}
		
		foreach(func_get_args() as $key) {
			$conf = $conf[$key];
		}
		
		return $conf;
	}
	
	/**
	 * Возвращает признак существования бандла
	 **/
	public function exists() {
	
	    if(\infuso\core\file::get($this->path()."/.infuso")->exists()) {
	        return true;
	    }

		if(\infuso\core\file::get($this->path()."/info.ini")->exists()) {
	        return true;
	    }
	    
	    return false;
	}
	
	/**
	 * Возвращает список директорий, которые не нужно удалять при обновлении
	 * Также в этих директориях не будет выполняться поиск вложенных бандлов
	 **/
	public function leave() {
	
		$ret = array();
	    $conf = $this->conf();
	    $leave = is_array($conf["leave"]) ? $conf["leave"] : array();
	    foreach($leave as $folder) {
			$ret[] = (string) file::get($this->path()."/".$folder);
	    }
	    return $ret;
	    
	}
	
	/**
	 * Возвращает список публичных директорий бандла
	 **/
	public function publicFolders() {

		$ret = array();
	    $conf = $this->conf();
	    $public = is_array($conf["public"]) ? $conf["public"] : array();
	    foreach($public as $folder) {
			$ret[] = (string) file::get($this->path()."/".$folder);
	    }
	    return $ret;

	}
	
	/**
	 * Возвращает путь к классам модуля
	 **/
	public function classPath() {
	    return file::get($this->path()."/class/");
	}

}
