<?

class ndra_tagcloud extends mod_controller {

	public static function indexTest() { return true; }
	public static function index() {
	    tmp::header();
	    tmp::reset();
	    for($i = 1; $i <= 10; $i++){
	        $mas["$i"]="#";
	    }
	    echo "<div style='background:black;'>";
	         self::create($mas)->fontSize(50)->flashvar("tcolor","0xAFAFFA")->width("500px")->height("500px")->exec();
	    echo "</div>";
	    tmp::footer();
	}

	// ----------------------------------------------------------------------------------------------

	/*doc: Пример */
	/*doc-code:
	self::create($tags)->width("100%")->height(200)->param("quality","low")->exec();
	*/

	// ----------------------------------------------------------------------------------------------

	private $tags = "";
	private $fontsize = 14;
	private $nTags = "";

	private $swfobject;


	public function __construct($tags=null) { //массив тегов key->value где ключ тайтл тега,значение его url
	    $this->tags = $tags;

	    $this->swfobject = ndra_swfobject::create("/ndra/res/tagcloud/tagcloud.swf");

	    //Задаем параметры по-умолчанию
	    $this->swfobject
	        ->width("200px")
	        ->height("200px")
	        ->param("scale","noscale")

	        ->param("wmode","transparent")
	        ->param("allowScriptAccess","always")

	        ->flashvar("tcolor","0xFFFFFF")
	        ->flashvar("tcolor2","0x000000")
	        ->flashvar("hicolor","0xB12AC8")
	        ->flashvar("tspeed","110")
	        ->flashvar("distr","true")
	        ->flashvar("mode","tags");
	}
	/*doc: ndra_tagcloud::create()
	Создает новый объект
	*/
	public static function create($tags) {
	    return new self($tags);
	}

	public function fontSize($fontsize) { $this->fontsize = $fontsize; return $this; }

	//Функция приводит массив тегов к необходимому виду для tagcloud.swf
	function normalizeTags() {
	    $tags = $this->tags;

	    $str="<tags>";
	    foreach($tags as $title=>$url){
	        $str.="<a href='$url' style='`font-size:".$this-> fontsize."px;'>$title</a>";
	    }
	    $str.="</tags>";
	    $str = urlencode($str);
	    $search = array("/!/g","/'/g","/\(/g","/\)/g","/\)/g","/\*/g");
	    $replace = array("%27","%28","%29","%2A");
	    $str = str_replace($search,$replace,$str);

	    //$this->nTags = $str;

	    return $str;
	}



	public function exec() {
	    $this->swfobject
	        ->flashvar("tagcloud", $this->normalizeTags())
	        ->exec();
	}


	//Используем интерфейс swfobject UPD: не самое красивое решение, возможно требует рефакторинга
	public function width($width) { $this->swfobject->width($width); return $this; }
	public function height($height) { $this->swfobject->height($height); return $this; }
	public function flashvar($key,$val) { $this->swfobject->flashvar($key,$val); return $this; }
	public function attribute($key,$val) { $this->swfobject->attribute($key,$val); return $this; }

}
