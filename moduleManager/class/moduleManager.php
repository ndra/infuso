<?

	class moduleManager extends mod_controller{

	/******************************************************************************/
	// Настройки прямого доступа

	public static function indexTest() { return mod_superadmin::check(); }
	
	public static function indexTitle() { return "Управление модулями"; }
	
	public static function index() {
	    admin::header("Управление модулями");

	    $tree = array();
	    foreach(mod::all() as $m) {

	        // Модуль
	        $module = array(
	            "text" => "<span style='font-weight:bold;'>$m<span>",
	            id=>$m,
	            "children" => array(),
	        );

	        // Файлы
	        $module["children"][] = array(
	            "text"=>"Файлы",
	            "icon" => "/moduleManager/icons/files.png",
	            "module"=>$m,
	            "className"=>"inx.mod.moduleManager.fileManager",
	        );

	        // Шаблоны
	        foreach(tmp_theme::all() as $theme)
	            if($theme->mod()==$m)
		            $module["children"][] = array(
		                "text" => $theme->name(),
		                "params"=> array(
							"themeID" => $theme->id()
						),
		                "className" => "inx.mod.moduleManager.templateManager",
		                "icon" => "template"
		            );

	        // Inx
	        if(mod::info($m,"inx","path"))
	            $module["children"][] = array(
	                "text"=>"inx",
	                "icon" => "/moduleManager/res/inx.gif",
	                "module"=>$m,
	                "className"=>"inx.mod.moduleManager.inx.manager"
	            );

	        // Таблицы
	        if(mod::info($m,"mysql","path"))
	            $module["children"][] = array(
	                "text"=>"Таблицы",
	                "icon" => "/moduleManager/icons/tables.png",
	                "module"=>$m,
	                "className"=>"inx.mod.moduleManager.tableManager"
	            );
	        if(sizeof($module[children]))
	            $tree[] = $module;

	    }

	    foreach($tree as $key=>$val) {
	        $tree[$key]["system"] = mod::info($val["id"],"moduleManager","pack");
	        if($tree[$key]["system"])
	            $tree[$key]["text"] = "<span style='color:gray;'>$val[id]</span>";
	    }

	    usort($tree,array("moduleManager","sort"));

	    inx::add(array(
	        "type" => "inx.mod.moduleManager.manager",
	        tree=>$tree
	    ));
	    tmp::exec("admin:footer");
	}

	public static function sort($a,$b) {
	    if($a["system"]!=$b["system"])
	        return $a<$b;
	    return strcmp(strtolower($a["text"]),strtolower($b["text"]));
	}

	public static function indexFailed() { admin::fuckoff(); }

	public static function postTest() { return mod_superadmin::check(); }
	public static function post_build() {
	    mod_classmap::buildClassMap();
	    reflex_init::init();
	    tmp_theme_init::init();
	    mod_init_events::init();
	}

}
