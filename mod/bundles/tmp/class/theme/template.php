<?

/**
 * Класс шаблона темы оформления
 * У одного шаблона могут быть несколько файлов в разных темах
 * Используется в редакторе тем
 **/
class tmp_theme_template extends mod_component {

	private $theme = null;
	private $name = null;
	public function __construct($theme=null,$name=null) {
	    $this->theme = $theme;
	    $this->name = "/".$name;
	    $this->name = preg_replace("/\/+/","/",$this->name);
	}

	/**
	 * Возвращает тему
	 **/
	public function theme() {
	    return $this->theme;
	}

	/**
	 * Возвращает полное имя шаблона
	 **/
	public function name() {
	    return $this->name;
	}

	/**
	 * Последняя часть имени шаблона
	 **/
	public function lastName() {
	    return file::get($this->name())->basename();
	}

	/**
	 * Возвращает имя шаблона относительно темы
	 **/
	public function relName() {
	    $name = file::get($this->name())->rel($this->theme()->base());
	    return $name;
	}

	/**
	 * Возвращает глубину шаблона
	 **/
	public function depth() {
	    return sizeof(util::splitAndTrim($this->name(),"/"));
	}


	/**
	 * Возвращает список дочерних шаблонов
	 **/
	public function children() {
	    $ret = array();
	    $root = $this->name();
	    if($root=="/") {
	        $root = "";
		}
	    
	    foreach($this->theme()->templates() as $tmp) {

	        if(substr($tmp->name(),0,strlen($root)+1)===$root."/") {
	            if($tmp->depth()==$this->depth()+1) {
	                $ret[] = $tmp;
				}
			}
	    }
	    
	    return $ret;
	}

	public function parent() {
	    $name = file::get($this->name())->up();
	    return new self($this->theme(),$name);
	}

	/**
	 * Удаляет файл шаблона с заданным расширением, например "css"
	 **/
	public function removeFile($ext) {
	    $file = $this->theme()->templateFile($this->name(),$ext);
	    $file->delete();
	}

	/**
	 * Возвращает файл заданного расширения
	 **/
	public function file($ext) {
	    $root = $this->theme()->path();
	    $path = $this->relName();
	    return file::get($root."/".$path.".".$ext);
	}

	/**
	 * Возвращает директорию шаблона в ФС
	 **/
	public function folder() {
	    $root = $this->theme()->path();
	    $path = $this->relName();
	    return file::get($root."/".$path);
	}

	public function setCode($code) {
	    $this->removeFile("php");
	    $this->file("php")->put($code);
	    $this->theme()->buildMap();
	}

	public function setJS($code) {
	    $this->removeFile("js");
	    $this->file("js")->put($code);
	    $this->theme()->buildMap();
	}

	public function setCSS($code) {
	    $this->removeFile("css");
	    $this->file("css")->put($code);
	    $this->theme()->buildMap();
	}

	public function contents($ext) {
	
	    if($this->file($ext)->exists())
	        return $this->file($ext)->contents();

	    if($ext=="php") {
	        $name = strtr($this->file($ext),array(".php" => ".inc.php"));
	        return file::get($name)->contents();
	    }

	}

	/**
	 * Добавляет дочерний шаблон
	 **/
	public function add($name) {
	    $file = $this->file("php")->up()."/".$this->file("php")->baseName()."/".$name.".php";
	    $file = file::get($file);
	    file::mkdir($file->up(),true);
	    $file->put("<"."? ");
	    $this->theme()->buildMap();
	}

	/**
	 * Принадлежит ли данный шаблон теме? (на основании имени шаблона и базового шаблона темы)
	 **/
	public function inTheme() {
	    return true;
	}

	public function delete() {
	
	    if(!$this->inTheme())
	        return;

	    if($this->relName()=="")
	        return;

	    $this->file("php")->delete();
	    $this->file("js")->delete();
	    $this->file("css")->delete();
	    $this->folder()->delete(1);
	    $this->theme()->buildMap();
	}

	/**
	 * Меняет название данного шаблона
	 **/
	public function rename($name) {
	    $this->file("php")->rename($this->file("php")->up()->path()."/$name.php");
	    $this->file("js")->rename($this->file("js")->up()->path()."/$name.js");
	    $this->file("css")->rename($this->file("css")->up()->path()."/$name.css");
	    $this->folder()->rename($this->folder()->up()->path()."/$name");
	    $this->theme()->buildMap();
	}

	public function firstComment() {
		$code = $this->contents("php");
		if(preg_match("/(\/\/[^\n]*\n)|(\/\*.*\*\/)/is",$code,$matches)) {
		    return $matches[0];
		}
		
	}



}
