<?

/**
 * Контроллер управления шаблонами
 **/
class moduleManager_templateManager extends mod_controller {

	public static function postTest() {
		return mod_superadmin::check();
	}

	/**
	 * Возвращает шаблон по его имени
	 **/
	public static function get($theme,$name) {
		if($name=="0") {
		    $name = "/";
		}
		$theme = tmp_theme::get($theme);
		$tmp = $theme->template($name);
		return $tmp;
	}

	/**
	 * Контроллер получаения списка шаблонов
	 **/
	public static function post_listTemplates($p) {
	
		$theme = tmp_theme::get($p["themeID"]);
		$tmp = $theme->template($p["id"] ? $p["id"] : $theme->base());
		
		$ret = array();

		foreach($tmp->children() as $item) {
		    $text = $item->lastName();
		    
		    if(trim($item->file("js")->contents())) {
		        $text.= "<span style='background:DodgerBlue;padding:2px 4px;margin-left:2px;color:white;border-radius:3px;' >js</span>";
		    }
		        
			if(trim($item->file("css")->contents())) {
		        $text.= "<span style='background:brown;padding:2px 4px;margin-left:2px;color:white;border-radius:3px;' >css</span>";
		    }
		    
		    $comment = strip_tags($item->firstComment());
		    if($comment) {
		    	$text.= " <span style='opacity:.5;font-style:italic;padding-left:5px;' >{$comment}</span>";
		    }

		    $ret[] = array(
				"text" => $text,
				"name" => $item->lastName(),
				"id" => $item->name(),
				"folder" => sizeof($item->children()),
				"editable" => true,
				"icon" => "template"
			);
		}
		
		return $ret;
	}

	/**
	 * Контроллер получения контента шаблона
	 **/
	public static function post_getContents($p) {

		$tmp = self::get($p["themeID"],$p["id"]);

		switch(strtolower($p["contentType"])) {
		    default:
			case "php":
				$contents = $tmp->contents("php");
				break;
			case "js":
				$contents = $tmp->contents("js");
				break;
			case "css":
				$contents = $tmp->contents("css");
				break;
		}
		return array(
			"code" => $contents,
		);
	}

	/**
	 * Контроллер отправки контента шаблона
	 **/
	public static function post_setContents($p) {
		$tmp = self::get($p["themeID"],$p["id"]);
		switch(strtolower($p["contentType"])) {
		    default:
			case "php":
				$tmp->setCode($p["code"]);
				break;
			case "js":
				$tmp->setJS($p["code"]);
				break;
			case "css":
				$tmp->setCSS($p["code"]);
				break;
		}
		tmp_render::clearRender();
		mod::msg("Шаблон сохранен");
	}

	/**
	 * Контроллер создания шаблона
	 **/
	public static function post_newTemplate($p) {
	    $tmp = self::get($p["themeID"],$p["id"]);
		$tmp->add($p["name"]);
		$reload = self::get($p["themeID"],$p["id"])->name();
		$theme = tmp_theme::get($p["themeID"]);
		if($reload==$theme->template($theme->base())->name()) {
		    $reload = 0;
		}
		return $reload;
	}

	/**
	 * Контроллер удаления шаблона
	 **/
	public static function post_deleteTemplate($p) {
		self::get($p["themeID"],$p["id"])->delete();
		$reload = self::get($p["themeID"],$p["id"])->parent()->name()."";
		$theme = tmp_theme::get($p["themeID"]);
		if($reload==$theme->template($theme->base())->name()) {
		    $reload = 0;
		}
		return $reload;
	}

	/**
	 * Экшн переименования шаблона
	 **/
	public static function post_renameTemplate($p) {
		self::get($p["themeID"],$p["id"])->rename($p["name"]);
		$reload = self::get($p["themeID"],$p["id"])->parent()->name();
		$theme = tmp_theme::get($p["themeID"]);
		if($reload==$theme->template($theme->base())->name()) {
		    $reload = 0;
		}
		return $reload;
	}

	/**
	 * Копирование старых шаблонов
	 **/
	public static function post_restoreTemplates($p) {
	
		$theme = tmp_theme::get($p["themeID"]);
		$folders = array(
		    "/eshop/templates/",
		    "/form/templates/",
		    "/user/templates/",
		    "/tmp/templates/",
	     	"/ndra/templates/",
	     	"/pay/templates/",
	     	"/reflex/templates/",
	     	"/vote/templates/",
	     	"/lang/templates/",
		);
		
		foreach($folders as $folder) {
		    $folder = file::get($folder);
		    if($folder->exists()) {
			    $dest = file::get($theme->path()."/".$folder->up());
			    file::get($dest->up()."/".$dest->basename().".php")->put("");
			    $folder->copy($dest);
		    }
		}
		
		$theme->buildMap();
	}

}
