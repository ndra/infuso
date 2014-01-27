<?

class ndra_content extends mod_controller {

	public static function indexTest() { return true; }
	public static function index() {
	    tmp::header();
	    tmp::reset();
	    $content = file::get("/ndra/data/test-content.txt")->data();
	    echo "<div style='margin:0px 20%;' >";
	    echo self::process($content);
	    echo "</div>";
	    tmp::footer();
	}

	private static $included = false;
	public static function process($src) {

		// Обрабатываем контент стандартным процессором
		$src = mod::service("contentProcessor")->process($src);

	    if(!self::$included)
	    tmp::exec("ndra:content.common");

	    if(mod::conf("ndra:content:replaceTags")) {
	        // Заменяем пропускаемые тэги затычками
	        foreach(array("code","script") as $tag)
	            $src = preg_replace_callback("/<{$tag}[^\>]*>.+?<\/{$tag}[^\>]*>/s",array(self,"freezeTag"),$src);

	        $src = preg_replace("/\r\n/","\n",$src);
	        $src = preg_replace("/[ ]*\n[ ]*/","\n",$src);

	        // Обрабатываем картинки
	        $src = preg_replace_callback("/((<img[^\>]*\>\s*)+)/",array(self,"replaceImages"),$src);

	        // Убираем лишние переводы строк вокруг блочных элементов
	        $src = preg_replace_callback("/(\n)*(\<[\s\/]*(div|h1|h2|ul|li|p|table|tbody|thead|widget|tr|td|caption|c10tk9rnk1z3mvrt810xpv038u2t4g)[^>]*\>)(\n)*/",array(self,"replaceBlockElement"),$src);
	        $src = strtr($src,array("\n"=>"<br/>\n"));
	    }

	    $src = preg_replace_callback("/\{([^{}]{0,50})\}/u",array(self,"replaceLink"),$src);

	    // Достаем затычки
	    $src = preg_replace_callback("/<c10tk9rnk1z3mvrt810xpv038u2t4g>(\d+)<\/c10tk9rnk1z3mvrt810xpv038u2t4g>/",array(self,"unfreezeTag"),$src);

	    return "<div class='content' >$src</div>";
	}

	private static $memory = array();
	public static function freezeTag($src) {
	    $n = sizeof(self::$memory);
	    self::$memory[] = $src[0];
	    return "<c10tk9rnk1z3mvrt810xpv038u2t4g>$n</c10tk9rnk1z3mvrt810xpv038u2t4g>";
	}
	public static function unfreezeTag($s) {
	    $ret = self::$memory[$s[1]];
	    if(preg_match("/^<code/",$ret)) {
	        ndra_syntax::add();
	        $ret = preg_replace("/^<code>/","",$ret);
	        $ret = preg_replace("/<\/code>$/","",$ret);
	        $ret = trim($ret);
	        $ret = htmlspecialchars($ret);
	        $ret = "<pre><code>$ret</code></pre>";
	    }
	    return $ret;
	}

	public static function replaceBlockElement($src) {
	    return $src[2];
	}

	public static function replaceImages($src) {
	    $xml = @simplexml_import_dom(DOMDocument::loadHTML("<meta http-equiv='content-type' content='text/html; charset=utf-8' />".$src[0]));
	    $data = array();
	    foreach($xml->xpath("//img") as $img)
	        $data[] = array(
	                "src" => $img->attributes()->src."",
	                "alt" => $img->attributes()->alt."",
	        );

	    ob_start();
	    tmp::exec("ndra:content.images",$data);
	    return ob_get_clean();
	}

	public static function replaceLink($link) {
	    $link = $link[1];
	    $item = reflex_meta_item::all()->like("links",",".mb_strtolower($link,"utf-8").",")->one();
	    if($item->exists())
	        return "<a href='{$item->item()->url()}' >$link</a>";

	    return $link;
	}

	// ----------------------------------------------------------------------------- Конфигурация

	// Возвращает все параметры конфигурации
	public static function configuration() {
	    return array(
	        array("id"=>"ndra:content:replaceTags","type"=>"checkbox","title"=>"Преобразовывать тэги и перевод на новую строку в текстовых полях"),
	    );
	}

	// -----------------------------------------------------------------------------

}
