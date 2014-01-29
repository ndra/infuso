<?

/**
 * Класс для экспорта данных в csv
 **/
class reflex_editor_export extends mod_controller {

	public static function indexTest() {
	    return user::active()->checkAccess("admin:showInterface");
	}

	public static function postTest() {
	    return user::active()->checkAccess("admin:showInterface");
	}

	public static function index() {
	}

	public static function post_doExport($p) {

		$page = $p["page"];
		$name = $p["name"];

		$limit = 100; // Число строк, обрабатываемое за раз

		$list = reflex_collection::unserialize($p["collection"])->limit($limit)->page($page);
		
		if(!$list->editor()->beforeCollectionView())
			return;

		$pages = $list->pages();

		if($page==1) {

			// Создаем папку для экспорта
			$dir = file::get("/reflex/export/");
		    file::mkdir($dir,1);
		    foreach($dir->search() as $oldFile)
		        $oldFile->delete(true);
		    file::get($dir."/.htaccess")->put("AddType application/octet-stream .csv");

		    $name = strtr(util::now()->num(),".: ","---").".csv";
		    $f = fopen(file::get("/reflex/export/".$name)->native(),"w+");

		    // Шапка таблицы
		    $header = array();
		    foreach($list->editor()->gridFields() as $field) {
		        $header[] = $field->label();
		    }
		    $header = implode(";",$header)."\n";
		    $header = mb_convert_encoding($header,"cp-1251","utf-8");
		    fwrite($f,$header);

		} else {
			$f = fopen(file::get("/reflex/export/".$name)->native(),"a+");
		}

	    // Записываем строки таблицы
	    foreach($list as $item) {
	        $row = array();
	        foreach($item->editor()->gridFields() as $field) {
	            $row[] = self::escape($field->rvalue());
	        }
	        $row = implode(";",$row)."\n";
		    $row = mb_convert_encoding($row,"cp-1251","utf-8");
		    fwrite($f,$row);
	    }

		return array(
		    "page" => $page+1,
		    "pages" => $pages,
		    "name" => $name,
		    "csv" => file::get("/reflex/export/".$name)->path().""
		);

	}

	public static function escape($str) {

		// Заменяем точку на запятую в числах
		if(is_float($str))
		    $str = strtr($str,array("."=>","));

		if(preg_match("/[\;\n\"]/",$str)) {
			$str = strtr($str,array('"'=>'""'));
			$str = '"'.$str.'"';
		}
	    return $str;
	}

}
