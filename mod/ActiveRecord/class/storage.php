<?

namespace infuso\ActiveRecord;

use \Infuso\Core\File;

/**
 * Класс-файловое хранилище для ActiveRecord
 **/
class Storage extends \Infuso\Core\Controller {

	private $class;
	private $id;
	private $path;

	public function __construct($class=null,$id=null,$path="/") {
	    $this->class = $class;
	    $this->id = $id;
	    $this->path = $path;
	}
	public function setPath($path) {
	    $this->path = $path;
        return $this;
	}

	public function get($class,$id,$path="/") {
	    return new self($class,$id,$path);
	}

	public function fromRequest($p) {
	    list($class,$id) = explode(":",$p["storage"]);
	    $ret = reflex::get($class,$id)->storage();
	    $ret->setPath($p["path"]);
	    return $ret;
	}
	
	/**
	 * Возвращает id записи ActiveRecord, с которой связан этот экземпляр хранилища
	 **/
	public function id() {
		return $this->id;
	}
	
	public function reflex() {
		return Record::get($this->class,$this->id);
	}

	public function defaultFolder() {
	    $c = explode("_",$this->class);
	    $mod = $c[0];
	    $class = strtr($this->class,array("\\" => "_"));
	    return \mod::app()->publicPath()."/files/{$class}/";
	}

	public function root() {
	    $ret = $this->reflex()->reflex_storageFolder();
	    if(!$ret)
	        $ret = $this->defaultFolder();
	    if($this->reflex()->reflex_storageUseMultipleFolders()) {
	        $key = $this->reflex()->id();
	        $primaryKeyPrefix = substr(md5($key),0,2);
	        $ret.= "/$primaryKeyPrefix/$key/";
	    }
	    return $ret;
	}

	public function path() {
	    $path = $this->root()."/".$this->path."/";
	    return $path;
	}

	public function exists() {
	    if(!$this->reflex()->exists()) {
			return false;
		}
	    return true;
	}

	public function prepareFolder() {
	    if(!$this->exists()) return;
	    $path = $this->root();
	    file::mkdir($path);
	    // Добавляем описание в папку хранилища только в случае множественных папок
	    if($this->reflex()->reflex_storageUseMultipleFolders())
	        file::get("$path/storage.descr")->put("{$this->class}:{$this->id()}");
	}

	public function files() {
	    if(!$this->exists()) return file_list::void();
	    return file::get($this->path())->dir()->exclude("storage.descr");
	}

	public function allFiles() {
	    if(!$this->exists()) return file_list::void();
	    return file::get($this->root())->search()->exclude("storage.descr");
	}

	public function totalSize() { return $this->size(); }
	
	public function size() { return $this->allFiles()->size(); }

	public function count() {
	    return $this->allFiles()->files()->length();
	}

	public static function normalizeName($str) {

	    $str = mb_strtolower($str,"utf-8");
	    $tr = array(
	        "й" => "y",
	        "ц" => "ts",
	        "у" => "u",
	        "к" => "k",
	        "е" => "e",
	        "н" => "n",
	        "г" => "g",
	        "ш" => "sh",
	        "щ" => "sh",
	        "з" => "z",
	        "х" => "h",
	        "ъ" => "",
	        "ф" => "f",
	        "ы" => "i",
	        "в" => "v",
	        "а" => "a",
	        "п" => "p",
	        "р" => "r",
	        "о" => "o",
	        "л" => "l",
	        "д" => "d",
	        "ж" => "zh",
	        "э" => "e",
	        "я" => "ya",
	        "ч" => "ch",
	        "с" => "s",
	        "м" => "m",
	        "и" => "i",
	        "т" => "t",
	        "ь" => "",
	        "б" => "b",
	        "ю" => "yu",
	    );
	    $str = strtr($str,$tr);
	    $str = preg_replace("/[^1234567890qwertyuiopasdfghjklzxcvbnm.]+/","_",$str);
	    $str = trim($str,"_");
	    return $str;
	}

	/**
	 * Добавляет закачанный файл в хранилище
	 **/
	public function addUploaded($src,$name) {
	    $name = self::normalizeName($name);
	    if(!$this->exists()) {
	        mod::msg("Вы пытаетесь закачать файл в несуществующий объект",1);
	        return;
	    }
	    $this->prepareFolder();
	    $path = $this->path()."";
	    $dest = $path.$name;
        file::mkdir($path);
	    file::moveUploaded($src,$dest);
	    $this->reflex()->reflex_afterStorage();
	    return file::get($dest)->path();
	}

	/**
	 * Добавляет файл в хранилище
	 **/
	public function add($src,$name) {
	    $name = self::normalizeName($name);
	    if(!$this->exists()) {
			return;
		}
	    $this->prepareFolder();
	    $path = $this->path()."";
	    $dest = $path.$name;
	    
	    if(file::get($src)->path() == "/") {
        	throw new Exception("reflex_storage::add() first argument cannot be void");
	    }
	    
	    file::get($src)->copy($dest);
	    $this->reflex()->reflex_afterStorage();
	    return file::get($dest)->path();
	}

	public function mkdir($name) {
	    if(!$this->exists()) return;
	    $this->prepareFolder();
	    file::mkdir("{$this->path()}/$name");
	}

	/**
	 * Удаляет файл из хранилища
	 **/
	public function delete($name) {
	    if(!$this->exists()) return;
	    $path = "{$this->path()}/$name";
	    file::get($path,1)->delete();
	    $this->reflex()->reflex_afterStorage();
	}

	/**
	 * Очищает хранилище, удаляет все файлы
	 **/
	public function clear() {
	    if(!$this->exists())
	        return;
	    $path = $this->path();
	    file::get($path,1)->delete(true);
	    $this->reflex()->reflex_afterStorage();
	}

	public function beforeChange() {
	    return $this->reflex()->reflex_beforeStorageChange();
	}

	public function beforeView() {
	    return $this->reflex()->reflex_beforeStorageView();
	}

	/**
	 * Добавляет файл из нативной файловой системы (или http) в хранилище
	 **/
	public function addNative($url,$name=null) {

	    if(!$url)
	        return;

	    if(!$name)
	        $name = strtolower(file::get($url)->name());

	    // Скачиваем файл во временную папку
	    $dir = file::tmp();
	    $data = file_get_contents($url);

	    if(!$data)
	        return false;

	    $tmpname = $dir."/".$name;
	    file::get($tmpname)->put($data);

	    // Добавляем файл в хранилище
	    $img = $this->add($tmpname,$name);

	    // Убираем временные файлы
	    file::get($tmpname)->delete(true);

	    return $img;
	}


	/**
	 * @todo вынести методы контроллера в отдельный класс
	 **/
	public static final function postTest($p) {
		return true;
	}

	/**
	 * Возвращает список файлов в хранилище
	 **/
	public static final function post_listFiles($p) {

	    $storage = self::fromRequest($p);

	    if(!$storage->beforeView())
	        return false;

	    $files = $storage->files();
	    $ret = array();
	    foreach($files as $file)
	        $ret[] = array(
	            name => $file->name(),
	            folder => $file->folder(),
	            icon => $file->preview()->width(100)->height(100)."",
	            url=>$file->url()
	        );
	    $status = "";
	    $status.= "Файлов: {$storage->count()}, ";
	    $size = ceil($storage->totalSize()/1024)." кб.";
	    $status.= "Объем: {$size}";
	    //mod_cmd::meta("status",$status);
	    return $ret;

	}

	// Возвращает превью файла на основании заданного урл
	public static function post_getPreview($p) {
	    return file::get($p["url"])->preview()->width(100)->height(100)."";
	}

	public static function post_getPreviews($p) {
	    $ret = array();
	    foreach($p["files"] as $file)
	        $ret[$file] = file::get($file)->preview()->width(100)->height(100)."";
	    return $ret;
	}

	public static function post_mkdir($p) {
	    $storage = self::fromRequest($p);
	    mod::msg($p);
	    if(!$storage->beforeChange()) {
	        mod::msg("Вы не можете создавать папки",1);
	        return false;
	    }
	    $storage->mkdir($p["name"]);
	}

	public static function post_delete($p) {
	    $storage = self::fromRequest($p);
	    if(!$storage->beforeChange()) {
	        mod::msg("Вы не можете удалять файлы",1);
	        return false;
	    }
	    foreach($p["files"] as $file)
	        $storage->delete($file);

	}

	// Закачивает файл в хранилище
	public static function post_upload($p,$files) {

	    $errors = array(
	        1 => "The uploaded file exceeds the upload_max_filesize directive in php.ini.",
	        2 => "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.",
	        3 => "The uploaded file was only partially uploaded.",
	        4 => "No file was uploaded.",
	        6 => "Missing a temporary folder.",
	        7 => "Failed to write file to disk.",
	        8 => "File upload stopped by extension."
	    );

	    switch($f = $files["file"]["error"]) {
	        case 0: break;
	        default:
	            $error = $errors[$f];
	            if(!$error)
	                $error = "Unknown error while uploading file. Code $f.";
	            mod::msg($error,1);
	            return;
	    }

	    $storage = self::fromRequest($p);
	    if(!$storage->beforeChange()) {
	        mod::msg("Вы не можете закачивать файлы",1);
	        return false;
	    }

	    $ret = $storage
	        ->addUploaded($files["file"]["tmp_name"],$files["file"]["name"]);
	    return $ret;
	}

}
