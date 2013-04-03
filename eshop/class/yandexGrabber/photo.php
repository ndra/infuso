<?

class eshop_yandexGrabber_photo extends mod_controller {

	public static function postTest() {
		return user::active()->check("admin:internal",1);
	}

	public static function post_search($p) {

		$query = $p["query"];

		if($p["first"]) {
			$id = end(explode(":",$p["itemID"]));
			$query = eshop::item($id)->editor()->grabSearchQueries();
			$query = $query[0];
			//mod_cmd::meta("query",$query);
		}

		$search = new self($query);
		return $search->search();
	}

	public static function post_previews($p) {
		$ret = array();
		foreach($p["images"] as $img) {
		    $ext = file::get(mod_url::get($img)->path())->ext();
		    $ext = mb_strtolower($ext,"utf-8");
		    $parent = file::tmp();
		    $tmp = file::get($parent."/".md5($img).".$ext");
		    $tmp->put(file_get_contents($img));
		    $preview = $tmp->preview(100,100)."";
		    $ret[$img] = array("preview"=>$preview,"descr"=>$tmp->width()."x".$tmp->width().", ".round($tmp->size()/1024)." кб.");
		}
		return $ret;
	}

	public static function post_save($p) {
		foreach($p["ids"] as $id) {
			$id = end(explode(":",$id));
			$item = eshop::item($id);
			if(!$item->editor()->beforeEdit()) {
			    mod::msg("Вы не можете редактировать товар",1);
			    return;
			}
			foreach($p["images"] as $img)
				$item->editor()->downloadPhoto($img["url"]);
		}
	}

	private $query = "";
	public function __construct($query=null) {
		$this->query = $query;
	}

	public function search() {

	    $ret = array();

		// ---------------------------------- Прямой поиск

		// Если пользователь набрал в строке запроса url картинки
		if(preg_match("/(http|https)\:\/\//",$this->query))
			$ret[] = array(
				"url" => $this->query,
				"descr" => "Прямой адрес",
				"preview" => $this->query,
			);

		// ----------------------------------

	    $url = "http://images.yandex.ru/yandsearch?".http_build_query(array(
	        "text" => $this->query,
	        "rpt" => $image,
	    ));
	    $html = file_get_contents($url);
	    $domxml = @DOMDocument::loadHTML($html);
	    $xml = simplexml_import_dom($domxml);

	    foreach($xml->xpath("//a") as $item) {

	        // Ищем превью
	        foreach($item->xpath("descendant::img") as $p)
	            $preview = $p->attributes()->src."";
	        if(!$preview) continue;

	        // Получаем адрес изображения
	        $url =  $item->attributes()->href."";
	        $url = parse_url($url);
	        $query = $url["query"];
	        parse_str($query,$params);
	        $img = $params["img_url"];
	        if(!$img) continue;
	        $url = "http://".$img;

	        $path = new mod_url($url);

	        $ret[] = array(
	            "url" => $url,
	            "descr" => $path->host(),
	            "preview" => $preview,
	        );
	    }

	    return $ret;
	}

	public function getOne() {
	    $r = $this->search();
	    return $r[0]["url"];
	}

}
