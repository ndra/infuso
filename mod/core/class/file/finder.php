<? class mod_file_finder implements Iterator{

// Итераторская шняга
private $items = array();
public function rewind() {
	$this->load();
	reset($this->items);
}

public function current() {
	$this->load();
	return current($this->items);
}

public function key() {
	$this->load();
	return key($this->items);
}

public function next() {
	$this->load();
	return next($this->items);
}

public function valid() {
	return $this->current() !== false;
}

// -----------------------------------------------------------------------------

/**
 * озвращает последний в списке файл
 **/
public function last() {
	$this->load();
	$ret = $this->items[sizeof($this->items)-1];
	if(!$ret)
	    $ret = mod_file::nonExistent();
	return $ret;
}

// -----------------------------------------------------------------------------

/**
 * Класс для поиска файлов
 **/


/**
 * Системный конструктор
 **/
private $path = null;
private function __construct($path) {
	$this->path = $path;
}

/**
 * Возвращает директорию, в которой производится поиск
 **/
public function path() {
	return $this->path;
}

/**
 * Конструктор для людей
 **/
public function get($path) {
	return new self($path);
}

/**
 * Если взывана с одним аргументом - вернет значение параметра
 * Если взывана с двумя аргументами - установит значение параметра
 **/
private function param($key,$val=0) {
	if(func_num_args()==1)
	    return $this->param[$key];
	if(func_num_args()==2) {
	    $this->param[$key] = $val;
	    return $this;
	}
}

/**
 * Возвращает / устанавливает имя файла, с которого нужно начать поиск
 * Это используется при поиске в несколько заходов
 **/
public function from($val=0) {
	if(func_num_args()==0) {
	    return $this->param["from"];
	} else {
	    $this->param["from"] = mod_file::normalizePath($val);
	    return $this;
	}
}

/**
 * Волшебная функция
 * Делает обертки для чтения/записи параметров
 **/
public function __call($method,$params) {

	if(!in_array($method,array(
	    "max",
	    "ext",
	))) {
	    throw new Exception("Method ".get_class($this)."::".$method." not found");
	}
	array_unshift($params,$method);
	return call_user_func_array(array($this,"param"),$params);
	
}

private $loaded = false;
private $counter;
public function load() {

	// Чтобы не загружать два раза
	if($this->loaded)
	    return;
	$this->loaded = true;
	
	$this->counter = 0;
	    
	
	$items = $this->scandir($this->path(),$items);
	foreach($items as $item) {
	    $this->items[] = $item;
	}
	
}

// Сканирует одну папку, возвращает результат
private function scandir() {

	$scan = @scandir(mod::root()."/".$dir);
	if(!$scan) return;
	sort($scan);

	$files = array();
	$ret = array();
	foreach($scan as $file) {
	
	    if($file=="." || $file=="..")
			continue;
		if($file==".svn")
		    continue;
		if($file==".git")
		    continue;
		if($file==".DS_Store")
		    continue;
        
		    
		$file = mod_file::get("/".$dir."/".$file);
		if(!$file->folder())
			$ret[] = $file;
	}
	
	return $ret;

}

}
