<?

namespace infuso\core;

class localFile extends file {

	private static $temporaryFolder = "/mod/_temp/";
	
	private static $foldersToDelete = array();

	public static $cache = array();

	public function __construct($path) {
	    $this->path = self::normalizePath($path);
	}
	
	/**
	 * Нормирует имя файла, убирая из него небезопасные символы типа ../
	 **/
	public static function normalizePath($path) {
	    $path = preg_replace("/\/+/","/",$path);
	    $path = preg_replace("/\.+/",".",$path);
	    $path = preg_replace("/[^\/._\-1234567890qwertyuiopasdfghjklzxcvbnm]/i","",$path);
	    $path = "/".trim($path,"/ ");
	    return $path;
	}

	/**
	 * @return striung Возвращает имя файла (без пути)
	 **/
	public function name() {
	    $name = explode("/",trim($this->path(),"/"));
	    return end($name);
	}

	/**
	 * @return string Имя файла без расширения
	 **/
	public function basename() {
	    $name = explode(".",$this->name());
	    return $name[0];
	}

	/**
	 * @return Возвращает расширение файла
	 **/
	public function ext() {
	    return end(explode(".",$this->path()));
	}

	public function url() {
		return $this->path;
	}

	/**
	 * Возвращает полный путь к файлу в ФС
	 **/
	public function native() {
	    return mod::root().$this->path;
	}

	/**
	 * @return Вернет true/false в зависимости от того, папка ли это
	 * Результаты рабоыт функции кэшируются
	 **/
	public function folder() {
	
		self::beginOperation("folder",$this);

		if(!array_key_exists($this."",self::$cache)) {
		    self::$cache[$this.""] = is_dir($this->native());
		}
		
		$this->endOperation();

	    return self::$cache[$this.""];
	}

	public function dir() {
	
	    self::beginOperation("dir",$this);
	
	    @$scandir = scandir($this->native());
	    $ret = array();
	    if($scandir)
	        foreach($scandir as $file)
	            if($file!="." && $file!=".." && $file!=".svn" && $file!=".git" && $file!=".DS_Store")
	                $ret[] = self::get($this->path."/".$file);
	    $ret = new flist($ret);
	    
	    $this->endOperation();
	    
	    return $ret;
	}

	private static function scandir($dir,&$ret) {
	
		$files = @scandir(mod::root()."/".$dir);
		if(!$files) return;
		foreach($files as $file) {

		    if($file=="." || $file=="..")
				continue;
			if($file==".svn")
			    continue;
			if($file==".git")
			    continue;
    		if($file==".DS_Store")
    		    continue;
            
			$path = self::normalizePath("/".$dir."/".$file);

		    if(strcmp($path,$from)<0)
		        continue;

			if(is_file(mod::root()."/".$dir."/".$file))
				$ret[] = self::get($dir."/".$file);
			else
			    self::scandir($dir."/".$file,$ret,$from);

		}
	}

	/**
	 * Возвращает список файлов внутри данной директории,
	 * работает рекурсивно
	 **/
	public function search() {
	
		self::beginOperation("search",$this);
	    $ret = array();
	    self::scandir($this->path(),$ret);
	    $ret = new flist($ret);
	    $this->endOperation();
	    return $ret;
	}

	/**
	 * Копирует фал или папку в папку $dest
	 **/
	public function copy($dest) {
		if($this->folder()) {
			self::mkdir($dest);
			foreach($this->dir() as $item) {
				$path = $dest."/".$item->rel($this->path());
				$item->copy($path);
			}
		} else {
		    $old_name = $this->native();
		    $new_name = self::get($dest)->native();
		    @copy($old_name,$new_name);
	    }
	}

	// Пакует папку или файл, сохраняет результат по указанному пути
	public function zip($path) {
	    $zip = new file_zip();
	    foreach($this->search() as $file)
	        if(!$file->folder()) {
	            $zip->addFile($file->contents(),$file->rel($this->path()));
	        }
	    self::get($path)->put($zip->file());
	}

	public function rel($path) {
	    $path = self::normalizePath($path);
	    $ret = "";
	    if(strpos($this->path(),$path)===0)
	        $ret = self::normalizePath(substr($this->path(),strlen($path)));
	    return $ret;
	}

    /**
	 * Возвращает содержимое файла
	 **/
	public function contents() {
		self::beginOperation("contents",$this);
		$ret = @file_get_contents($this->native());
		$this->endOperation();
		return $ret;
	}

	/**
	 * Записывает данные в файл
	 **/
	public function put($contents) {
	    self::beginOperation("put",$this);
		file_put_contents($this->native(),$contents);
		$this->endOperation();
		return $this;
	}

	/**
	 * Удаляет файл или папку
	 **/
	public function delete($recursive=null) {
	
		self::beginOperation("delete",$this);

	    if(!$this) {
	        $this->endOperation();
			return;
		}
		
	    if(strlen($this->path())<3) {
	        $this->endOperation();
			return; // Защита от дурака :)
		}

	    if($recursive)
	        foreach($this->dir($path) as $item)
	            $item->delete(true);

	    @unlink($this->native());
	    @rmdir($this->native());
	    
	    $this->endOperation();
	    
	    return $this;
	}

	/**
	 * Возвращает размер файла
	 **/
	public function size() {
		return @filesize($this->native());
	}

	public function inc() {
		self::beginOperation("inc",$this);
		$ret = include $this->native();
		$this->endOperation();
		return $ret;
	}

    /**
     * Переименовывает файл
     * $newName - новый путь к файлу от корня сайта
     **/
	public function rename($newName) {
	    $new_name = self::get($newName)->native();
	    @rename($this->native(),$new_name);
	}

	public function up() {
	    $path = $this->path();
	    $path = explode("/",trim($path,"/"));
	    array_pop($path);
	    return self::get(implode("/",$path));
	}

	public function ini($s=false) {
		self::beginOperation("ini",$this);
		$ret = @parse_ini_file($this->native(),$s);
		$this->endOperation();
		return $ret;
	}

	public function mod() {
	    $path = explode("/",trim($this->path(),"/"));
	    return $path[0];
	}

	/**
	 * Возвращает объект превью-генератора
	 **/
	public function preview($width=100,$height=100) {
	    
	    if(func_num_args()==0) {
	    	return new \file_preview($this->path());
		}
		
		if(func_num_args()==2) {
	    	return new \file_preview($this->path(),$width,$height);
	    }
		
	}

	/**
	 * Возвращает ширину картинки, если файл является картинкой
	 **/
	public function width() {
		list($width, $height)= @getimagesize($this->native());
		return $width;
	}

	/**
	 * Возвращает высоту картинки, если файл является картинкой
	 **/
	public function height() {
		list($width, $height)= @getimagesize($this->native());
		return $height;
	}

	public function imageType() {
	    $p = @getimagesize($this->native());
	    $ret = @image_type_to_extension($p[2]);
	    $ret = trim($ret,".");
		if($ret=="jpeg") $ret = "jpg";
		return $ret;
	}
	
	/**
	 * Возвращает объект бандла файла
	 **/
	public function bundle() {
	
	    $file = $this;
	    while($file->path() != "/" && !file::get($file->path()."/.infuso")->exists() ) {
	        $file = $file->up();
	    }
	    return new \infuso\core\bundle\bundle($file);
	}

	/**
	 * Возвращает следующую за текущей директорию, если идти по всему дереву
	 * Если запустить $dir = $dir->walk(), начав с корня, то вы обойдете все папки на всех уровнях
	 **/
	public function walk() {

		$src = $this;

		// Если у папки есть дочерние элементы, идем в первый
		foreach(file::get($src)->dir() as $folder)
		    if($folder->folder())
		        return $folder;

		// Если у папки есть соседи, идем в первый
		$up = file::get($src)->up();
		while(1) {

			foreach($up->dir() as $item) {

			    if(!$item->folder())
			        continue;

				if(strcmp($item->path()."",$src)>0)
		    		return $item;
			}

			if($up=="/")
			    return file::nonExistent();

			$up = $up->up();

		}
	}

}
