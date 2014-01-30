<? class ndra_hover extends mod_controller {

public static function indexTest() { return true; }
public static function index() {
    tmp::header();
    inx::add();
    $img1 = file::get("/dra/files/dra_portfolio/73/67/1_1278510740754.jpg")->preview(100,100)->crop()->desaturate();
    $img2 = file::get("/dra/files/dra_portfolio/73/67/1_1278510740754.jpg")->preview(110,110)->crop();
    self::create($img1,$img2)->href("http://www.yandex.ru")->exec();
    self::create($img1,$img2)->exec();
    self::create($img1,$img2)->exec();
    tmp::footer();
    
}

// -----------------------------------------------------------------------------

private $img1 = "";
private $img2 = "";
private $href = "";
private $popup = "";
public function __construct($img1=null,$img2=null) { $this->img1 = $img1; $this->img2 = $img2; }
public static function create($img1,$img2) { return new self($img1,$img2); }

public function href($href) { $this->href = $href; return $this; }

public function exec() {
    tmp::jq();
    tmp::js("/ndra/res/hover/hover.js");
    list($w1,$h1) = getimagesize(file::get($this->img1)->native());
    list($w2,$h2) = getimagesize(file::get($this->img2)->native());
    $popup = htmlspecialchars($this->popup,ENT_QUOTES);
    echo "<a ndra:popup='$popup' class='ndra-popup ndra-hover' style='display:block;position:relative;width:{$w1}px;height:{$h1}px;' href='{$this->href}' >\n";
    echo "<img src='$this->img1' style='position:absolute;width:{$w1}px;height:{$h1}px;' ndra:img2='{$this->img2}' ndra:w2='$w2' ndra:h2='$h2' />\n";
    echo "</a>\n";
}

} ?>
