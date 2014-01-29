<?

/**
 * Класс для компиляции и упаковки inx-компонентов
 **/
class inx_mount_file extends mod_component {

	private static $buf = array();

    private $path = "";

	private $directives = null;
	
	private $src = null;

	private $fullCode = null;
	
	private $fullDirectives = array();

    private $compiled = "";

	public static function get($path) {
		$path = file::get($path)->path();
		if(!self::$buf[$path]) {
			self::$buf[$path] = new self($path);
		}
		return self::$buf[$path];
	}
	
	public function __construct($path=null) {
		$this->path = $path;
	}
	
	public function conf() {
	    return array(
			"dest" => mod::app()->publicPath()."/inx/",
			"pack" => false
		);
	}

    /**
     * Компилирует компонент
     * Если компонент входит в состав другого компонента, ничего не делает
     **/
	public function compile() {
	    if(!$this->isDirective("link_with_parent")) {
      		$dest = $this->dest();
			file::mkdir(file::get($dest)->up()->path(),1);
			file::get($dest)->put($this->compiled());
        }
	}

	public function path() {
		return $this->path;
	}
	
	public function mod() {
        return file::get($this->path())->bundle()->path();
    }

	/**
	 * Возвращает имя компонента
	 * Например, inx.mod.reflex
	 **/
	public function name() {
	
	    $inxPath = mod::service("bundle")->bundle($this->mod())->conf("inx","path");
	    $path = file::get($this->mod()."/".$inxPath)->path();
	
		$ext = file::get($this->path())->ext();
		$name = strtr($this->path(),array($path=>"",".$ext"=>""));
		$name = file::get($name)->path();
		$name = strtr($name,"/",".");
		
		$name = mod::service("bundle")->bundle($this->mod())->conf("inx","namespace").".".trim($name,".");

		return $name;
	}

    public function lastName() {
        return end(explode(".",$this->name()));
    }

	/**
	 * Возвращает исходный код компонента
	 **/
	public function src() {
		if(!$this->src) {
		    $this->src = file::get($this->path())->data();
			preg_match("/^(\/\/ @[^\n]+\n)+/", $this->src, $matches);
			$this->directives = array();
			foreach(util::splitAndTrim($matches[0],"\n") as $dir) {
		        preg_match("/@((\w+)\s*(.*))/",$dir,$matches2);
				$this->directives[] = array(name=>$matches2[2],value=>$matches2[3]);
			}
			$this->src = preg_replace("/^(\/\/ @[^\n]+\n)+/", "", $this->src);
		}
		return $this->src;
	}

	/**
	 * Возвращает исходный код + слинкованные файлы
	 **/
	public function fullCode() {
		if(!$this->fullCode) {

		    $this->fullCode = "/*-- {$this->path()} --*/\n\n";
		    $this->fullCode.= $this->src();
		    $this->fullCode.="\n\n";
		    $dirs = $this->directives();

		    foreach($this->linked() as $inc) {
		        $this->fullCode.= $inc->fullCode();
		        foreach($inc->fullDirectives() as $dir) {
		            $dirs[] = $dir;
				}
		    }

		    $include = array();

		    foreach($dirs as $dir)
		        if($dir["name"]=="include")
		            foreach(util::splitAndTrim($dir["value"],",") as $inc)
		                $include[] = trim($inc);

			$include = array_unique($include);
			
			$this->fullDirectives = array();
			if(sizeof($include)) {
				$this->fullDirectives[] = array("name"=>"include","value"=>implode(",",$include));
			}

		}
		return $this->fullCode;
	}

    /**
     * Возвращает длируетивы компонента с учетом слинкованных компонентов
     **/
	public function fullDirectives() {
		$this->fullCode();
		return $this->fullDirectives;
	}

	/**
	 * Возвращает откомпилитрованный код
	 **/
	public function compiled() {
	
	    $conf = self::conf();
	
		if(!$this->compiled) {

		    $this->compiled = "";

		    foreach($this->fullDirectives() as $dir) {
		    	$this->compiled.= "// @$dir[name] $dir[value]\n";
            }

			try {
				$this->compiled.= $conf["pack"] ? inx_JSMin::minify($this->fullCode()) : $this->fullCode();
			} catch (Exception $ex) {
			    mod::msg($this->path().": ".$ex->getMessage(),1);
			}
		}
		return $this->compiled;
	}

	/**
	 * Возвращает список директив модуля
	 **/
	public function directives() {
		$this->src(); // Вызываем загрузку кода
		return $this->directives;
	}

	/**
	 * Проверяет наличие заданной директивы
	 **/
	public function isDirective($name) {
		foreach($this->directives() as $dir) {
		    if($dir["name"]==$name) {
		        return true;
            }
        }
	}

    /**
     * Возвращает значение директивы
     **/
	public function directive($name) {
		foreach($this->directives() as $dir) {
		    if($dir["name"]==$name) {
		        return $dir["value"];
            }
        }
	}

    public function priority() {
        return $this->directive("priority");
    }

    /**
     * Возвращает список слинкованных дочерних элементов
     **/
	public function linked() {
		$ret = array();
		foreach($this->children() as $child) {
		    if($child->isDirective("link_with_parent")) {
		        $ret[] = $child;
            }
		}
		return $ret;
	}

    /**
     * Возвращает дочерние элементы данного компонента
     **/
	public function children() {
	
		$folder = strtr($this->path(),array(".js"=>""));
		$ret = array();
		foreach(file::get($folder)->dir() as $file) {
            if($file->ext()=="js") {
    		    $file = self::get($file->path());
    		    $ret[] = $file;
            }
		}

        usort($ret,function($a,$b){

            if($d = $b->isDirective("link_with_parent") - $a->isDirective("link_with_parent")) {
                return $d;
            }

            if($d = $b->priority() - $a->priority()) {
                return $d;
            }

            return strcmp($a->path(),$b->path());
        });

		return $ret;
	}

	/**
	 * Возврщает целевой путь - куда будет скомпилирован компонент
	 **/
	public function dest() {
	    $conf = self::conf();
		$ret = $conf["dest"];
		$ret.= strtr($this->name(),".","/");
		$ret.= ".".file::get($this->path())->ext();
		return $ret;
	}

    /**
     * Компилирует компонент
     * Если компонент приоинкован к родителю, компилирует родительский компонент
     **/
	public function compileChain() {

		if(!$this->isDirective("link_with_parent")) {
		    $this->compile();
		    return;
		}
		$up = trim(file::get($this->path())->up()->path(),"/").".js";
		self::get($up)->compileChain();
	}

}
