<? class ndra_inflect extends mod_controller {

public static function indexTest() { return true; }
public static function index() {
	tmp::header();
	for($i=1;$i<6;$i++)
	    echo ndra_inflect::inflect("Жопа",$i)."<br/>";
	tmp::footer();
}

public static function inflect($word,$inflection) {
	$xml = simplexml_load_file("http://export.yandex.ru/inflect.xml?name=$word");
	foreach($xml->inflection as $inf)
	    $ret[$inf["case"]*1] = $inf."";
	$ret = $ret[$inflection];
	if(!$ret) $ret = $word;
	return $ret;
}

} ?>
