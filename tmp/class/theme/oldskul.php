<?

/**
 * Класс олдскульной темы оформления - раньше они были шаблонами
 **/
class tmp_theme_oldskul extends tmp_theme {

	private $name = null;
	private $path = null;
	private $base = null;
	private $id = null;
	private $mod = null;

	/**
	 * Конструктор для олдскульной темы
	 * @param $id имя темы (название)
	 * @param $path путь к файлам темы
	 * @param $base начальный шаблон, в который будут вложены шаблоны, найденные в папке темы
	 **/
	public function __construct($params=null) {
		$this->id = $params["id"];
		$this->path = mod_file::get($params["path"])->path();
		$this->base = $params["base"];
	}

	public function mod() {
		return file::get($this->path())->mod();
	}

	/**
	 * @return Название темы
	 **/
	public function id() {
		return $this->id;
	}

	public function base() {
		return $this->base;
	}

	public function name() {
		return "old:".$this->base();
	}

	public function constructorParams() {
		return array(
		    "id" => $this->id(),
		    "path" => $this->path(),
		    "base" => $this->base,
		    "mod" => $this->mod,
		);
	}

	/**
	 * @return Путь к файлам темы
	 **/
	public function path() {
		return $this->path;
	}

	public function autoload() {
		return true;
	}

}
