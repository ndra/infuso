<?

/**
 * Класс-билдер для скрипта slideshow.js
 **/
class ndra_slideshow extends mod_component {

	/**
	 * Подключает слайдшоу
	 **/
	public static function inc($version=1) {
		tmp::jq();
		mod::coreJS();
		tmp::singlejs("/ndra/res/slideshow/v{$version}/slideshow.js");
		tmp::css("/ndra/res/slideshow/v{$version}/slideshow.css");
	}

	/**
	 * Конструктор
	 **/
	public static function create($loader=null) {
		return new self($loader);
	}

	/**
	 * Конструктор
	 **/
	public function __construct($loader=null) {
		$this->param("loader",$loader);
		$this->param("version",1);
	}

	/**
	 * Перемотать на фотографию с номером $n
	 **/
	public function select($n) {
		$this->param("select",$n);
		return $this;
	}
	
	public function version($v) {
	    $this->param("version",$v);
	    return $this;
	}

	/**
	 * Привязывает к jQuery-селектору событие click, которое открывает эту галерею
	 **/
	public function bind($selector) {

		self::inc($this->param("version"));
		$params = json_encode($this->params());
		$js = "";
		$js.= "$(function(){";
		$js.= "ndra.slideshow.bind('$selector',$params);";
		$js.= "})";
		tmp::script($js);
	}

}
