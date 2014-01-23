<?

/**
 * Контроллер поиска
 **/
class reflex_search extends mod_controller {

	/**
	 * Видимость для браузеров
	 **/
	public static function indexTest() {
		return true;
	}
	
	/**
	 * Видимость для POST-запросов
	 **/
	public static function postTest() {
		return true;
	}
	
	/**
	 * Контроллер поиска
	 **/
	public static function index() {
	    $items = self::search($_GET["q"])->page($_GET["p"]);
	    tmp::exec("reflex:searchResults",$items);
	}

	public static function post_search($p) {
	    $items = self::search($p["q"])->limit(10);
	    $ret = array();
	    foreach($items as $item) {
	        $ret[] = array(
				"html" => $item->item()->reflex_smallSearchSnippet(),
				"url" => $item->item()->url()
			);
		}
	    return $ret;
	}

	public static function search($q) {
	    
	    $q = trim($q);
	    
	    // Если запрос пустой, возвращаем пустой результат
	    if(!$q) {
	        return reflex_meta_item::all()->eq("id",0);
		}

	    // Убираем из поискового запроса по одному слову справа, расширяя запрос, пока что-нибудь не найдется
	    while($q) {
	    
	        $items = reflex_meta_item::all()->param("pageurl","?q=".urlencode($q)."&p=%page%");
	        $items->desc("searchWeight");
	        
	        foreach(util::splitAndTrim($q," ") as $word) {
	            $items->like("search",$word);
	        }
	        
	        if($items->count()) {
	            return $items;
	        }

	        // Убираем слово справа и ищем еще раз
	        $q = preg_replace('/\s*\S*$/',"",$q);
	    }

	    return reflex_meta_item::all()->eq("id",0);
	}

}
