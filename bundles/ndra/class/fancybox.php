<?

class ndra_fancybox extends mod_controller {

public static function indexTest() {
	return true;
}

public static function index() {
	tmp::header();
	$img1 = file::get("/ndra/res/example/test1.jpg")->preview();
	$img2 = file::get("/ndra/res/example/test2.jpg")->preview();
	echo "<a class='fancybox' href='/ndra/res/example/test1.jpg' ><img src='$img1' /></a>";
	echo "<a class='fancybox' href='/ndra/res/example/test2.jpg' ><img src='$img2' /></a>";
	ndra_fancybox::add(".fancybox");
	ndra_fancybox::add(".fancybox");
	ndra_fancybox::add(".fancybox");
	tmp::footer();
}

private static $added = array();
public static function add($selector) {
	// Два раза не добавляем одно и то же
	if(in_array($selector,self::$added)) return;
	
	self::$added[] = $selector;
	tmp::jq();
	tmp::script("\$(function() {\$('$selector').fancybox();});");
	tmp::js("/ndra/res/fancybox/jquery.fancybox-1.3.4.pack.js");
	
	// Подключаем разные версии css для less и без
	if(tmp_render::less())
		tmp::css("/ndra/res/fancybox/jquery.fancybox-1.3.4.less",1000);
	else
	    tmp::css("/ndra/res/fancybox/jquery.fancybox-1.3.4.css",1000);
}

}
