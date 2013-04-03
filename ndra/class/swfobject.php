<?

class ndra_swfobject extends mod_controller {

	public static function indexTest() { return true; }
	public static function index() {
	    tmp::bodyClass("xxx");
	    tmp::header();
	    tmp::reset();
	    self::create("/ndra/res/swfobject/test.swf")->width("100%")->param("quality","low")->exec();
	    self::create("/ndra/res/swfobject/test.swf")->height(200)->exec();
	    tmp::footer();
	}

	// ----------------------------------------------------------------------------------------------

	/*doc: Пример */
	/*doc-code:
	self::create("/ndra/res/swfobject/test.swf")->width("100%")->height(200)->param("quality","low")->exec();
	*/

	// ----------------------------------------------------------------------------------------------

	private $id = null;
	private $src = "";
	private $width = 100;
	private $height = 100;
	private $flashvars = array();
	private $attributes = array();

	public function __construct($src=null) {
		$this->src = $src;
		$this->id(util::id());
	}

	/*doc: ndra_swfobject::create()
	Создает новый объект
	*/
	public static function create($src) {
	    $ret = new self($src);
	    $ret->noflash("<p><a href='http://www.adobe.com/go/getflashplayer'><img src='http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif' alt='Get Adobe Flash player' /></a></p>");
	    return $ret;
	}

	/*doc: ndra_swfobject::noflash($key,$val)
	...
	*/
	private $noflash = "";
	public function noflash($txt) {
	    $this->noflash = $txt;
	    return $this;
	}

	/*doc: ndra_swfobject::width($width)
	Устанавливает ширину объекта.
	Если единицы width не указаны, используются пиксели.
	*/
	public function width($width) { $this->width = $width; return $this; }

	/*doc: ndra_swfobject::height($height)
	Устанавливает высоту объекта.
	Если единицы height не указаны, используются пиксели.
	*/
	public function height($height) { $this->height = $height; return $this; }

	/*doc: ndra_swfobject::flashvar($key,$val)
	...
	*/
	public function flashvar($key,$val) { $this->flashvars[$key] = $val; return $this; }

	/*doc: ndra_swfobject::attribute($key,$val)
	...
	*/
	public function attribute($key,$val) { $this->attributes[$key] = $val; return $this; }

	/*doc: ndra_swfobject::param($key,$val)
	...
	*/

	public function id($id=null) {
		if(func_num_args()==0) {
		    return $this->id;
		} elseif(func_num_args()==1) {
		    $this->id = $id;
		    return $this;
		}
	}

	/*doc: ndra_swfobject::exec()
	Выводит флэш-объект.
	*/
	public function exec() {
		tmp::js("/ndra/res/swfobject/swfobject.js");

		$id = $this->id();
		$width = $this->width;
		$height = $this->height;
		if(is_integer($width)) $width.= "px";
		if(is_integer($height)) $height.= "px";

		echo "<div id='$id' style='width:$width;height:$height;' >\n";
		echo $this->noflash;
		echo "</div>";

		$flashvars = json_encode($this->flashvars);
		$params = json_encode($this->params());
		$attributes = json_encode($this->attributes);

		echo "<script type='text/javascript'>\n";
		echo "swfobject.embedSWF('{$this->src}', '$id', '$width', '$height', '9.0.0', '/ndra/res/swfobject/expressInstall.swf',$flashvars,$params,$attributes);\n";
		echo "</script>\n";

	}

}
