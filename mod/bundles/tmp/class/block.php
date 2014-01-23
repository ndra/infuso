<?

/**
 * Класс описывает блок. Блок - это определенная область на странице. У каждого блока есть
 * имя, например "header" или "right". Количество и имена блоков вы определяете сами.
 * tmp::block("center")->add("mysite:text"); // Добавить в блок center шаблон текст
 * tmp::block("center")->exec(); // Вывести содержимое блока center
 **/
class tmp_block {

	private $name = null;

	private static $buffer = array();
	
	private $templates = array();

	public function __construct($name) {
		$this->name = $name;
	}

	/**
	 * Возвращает блок по его имени.
	 **/
	public function get($name) {
		if(!self::$buffer[$name])
		    self::$buffer[$name] = new self($name);
		return self::$buffer[$name];
	}

	/**
	 * Добавляет в данный блок шаблон, виджет или любой другой объект класса,
	 * расшираящего tmp_generic
	 * Если передать строку - добавит шаблон
	 * @return Возврашает добавленный объект
	 **/
	public function add($generic) {
		if(is_string($generic)) {
		    $generic = tmp::get($generic);
		}
	    $this->templates[] = $generic;
	    return $generic;
	}

	/**
	 * Добавляет шаблон или виджет в начало блока
	 **/
	public function prepend($generic) {
		if(is_string($generic)) {
		    $generic = tmp::get($generic);
		}
	    array_unshift($this->templates,$generic);
	    return $generic;
	}

	/**
	 * Выводит содержимое блока
	 **/
	public function exec($prefix,$suffix) {
	    foreach($this->templates as $template) {
	        $r = $template->rexec();
	        if(trim($r)) {
		        echo $prefix;
		        echo $r;
		        echo $suffix;
	        }
	    }
	}

	public function templates() {
		return $this->templates;
	}

	public function items() {
		return $this->templates;
	}

	/**
	 * Возвращает количество шаблонов в блоке
	 **/
	public function count() {
		return sizeof($this->templates);
	}

}
