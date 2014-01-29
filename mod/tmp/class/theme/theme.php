<?

/**
 * Класс темы оформления
 * Тема представляет собой набор шаблонов.
 * Темы можно подключать и отключать
 **/
abstract class tmp_theme extends mod_component {

	private static $buffer = array();
	private static $defaultsLoaded = null;
	private $descr = null;

	/**
	 * @return Путь к файлам темы
	 **/
	abstract public function path();

	/**
	 * @return Название темы
	 **/
	abstract public function name();

	/**
	 * @return Приоритет темы, по умолчанию 0
	 **/
	public function priority() {
		return 0;
	}

	/**
	 * @return Возвращает объект темы по ID
	 **/
	public function get($id) {

		if(!self::$buffer[$id]) {
			$path = self::mapFolder()."/".$id.".php";
			$descr = file::get($path)->inc();
			$class = $descr["class"];
			$theme = new $class($descr["constructor"]);
			$theme->setDescr($descr);
	        self::$buffer[$id] = $theme;
		}
		return self::$buffer[$id];
	}

	/**
	 * @return Возвращает массив из объектов всех доступных тем
	 * Элемент массива - объект темы
	 **/
	public function all() {
		$themes = array();
		foreach(file::get(self::mapFolder()."/_themes.php")->inc() as $theme) {
		    $themes[] = self::get($theme);
		}
		return $themes;
	}

	/**
	 * @return Название темы
	 **/
	public function id() {
		return get_class($this);
	}

	public function setDescr($descr) {
		$this->descr = $descr;
	}

	/**
	 * @return Возвращает путь к файлу темы
	 * Если у темы нет соответствующего файла, возвращает null
	 **/
	public function templateFile($template,$ext) {
		$template = preg_replace("/[\:\.\/]+/","/",$template);
		$template = trim($template,"/");
		$file = $this->descr["map"][$template][$ext];
		if($file)
			return file::get($file);
		return file::nonExistent();
	}

	/**
	 * @return Возвращает путь к файлу темы
	 * Если у темы нет соответствующего файла, возвращает null
	 **/
	public function templateExists($template) {
		self::loadDefaults();
		$template = preg_replace("/[\:\.\/]+/","/",$template);
		$template = trim($template,"/");
		return array_key_exists($template,$this->descr["map"]);
	}

	/**
	 * @return Возвращает папку, в которую складываются описания шаблонов
	 **/
	public function mapFolder() {
		return mod::app()->varPath()."/tmp/themes/";
	}
	
	public function codeRenderFolder() {
	    return mod::app()->varPath()."/tmp/render-php/";
	}

	/**
	 * @return Возвращает путь к карте данной темы
	 **/
	public function mapFile() {
		return self::mapFolder()."/".$this->id().".php";
	}

	/**
	 * @return Возвращает параметры для конструктора данной темы
	 **/
	public function constructorParams() {
		return null;
	}

	/**
	 * @return Возвращает корневой шаблон темы
	 **/
	public function base() {
		return "/";
	}

	/**
	 * Возвращает модуль, в котором находится эта тема
	 **/
	public function bundle() {
		return self::inspector()->bundle();
	}

	/**
	 * Должна ли тема подключаться автоматически?
	 **/
	public function autoload() {
		return false;
	}

	/**
	 * Сохраняет описние и структуру файлов темы в файл
	 **/
	public function buildMap() {

		$map = array(
		    "class" => get_class($this),
		    "constructor" => $this->constructorParams(),
		    "map" => array(),
		);

		foreach(file::get($this->path())->search() as $file) {

		    if($file->ext()=="php") {
			    $renderPath = self::codeRenderFolder().$file->path();
			    file::mkdir(file::get($renderPath)->up());
			    $parser = new tmp_preparser();
			    $php = $parser->preparse($file->data());
			    file::get($renderPath)->put($php);
		    } else {
		        $renderPath = $file."";
		    }

		    $rel = file::get(file::get($file)->rel($this->path()));
		    $name = $this->base()."/".$rel->up()."/".$rel->basename();
		    $name = trim($name,"/");
		    $name = preg_replace("/[\:\.\/]+/","/",$name);
		    $ext = $file->ext();
		    $map["map"][$name][$ext] = $renderPath;
		}

		util::save_for_inclusion($this->mapFile(),$map);
	}

	/**
	 * @return class tmp_theme_template (не путать с tmp_template)
	 **/
	public function template($path="/") {
		$tmp = new tmp_theme_template($this,$path);
		return $tmp;
	}

	/**
	 * @return Массив со всеми шаблонами темы
	 **/
	public function templates() {
		$ret = array();
		foreach($this->descr["map"] as $path=>$tdesr) {
		    $ret[] = new tmp_theme_template($this,$path);
		}
		return $ret;
	}
	
	/**
	 * @return Массив со всеми шаблонами темы
	 **/
	public function templatesArray() {
		return $this->descr["map"];
	}

	/**
	 * Загружает дефолтные темы
	 **/
	public function loadDefaults() {
		if(self::$defaultsLoaded)
			return;
		self::$defaultsLoaded = true;
		foreach(file::get(tmp_theme::mapFolder()."/_autoload.php")->inc() as $themeID)
			tmp::theme($themeID);
	}

}
