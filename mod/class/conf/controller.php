<?

/**
 * Контроллер управления настройками из админки
 **/
class mod_conf_controller extends mod_controller {

	public static function indexTest() {
		return mod_superadmin::check();
	}

	public static function indexTitle() {
		return "Конфигурация";
	}

	public static function indexFailed() {
		admin::fuckoff();
	}

	public static function index() {

		admin::header("Конфигурация");
		$conf = array();

		// Собираем данные из mod.ini
		foreach(mod::all() as $mod)
			if($call = mod::info($mod,"mod","conf")) {

			    if(!is_array($call))
					$call = array($call);

				foreach($call as $callback) {
					$items = mod::call($callback);
					foreach($items as $item)
	                    $conf[$mod][] = $item;
				}

			}

		// Собираем данные через ООП
		foreach(mod::classes("mod_conf") as $class) {
		    $item = new $class;
			$name = $item->name();
			if(!$conf[$name])
			    $conf[$name] = array();
			$conf[$name] = array_merge($conf[$name],$item->conf());
		}

		echo "<form style='padding:40px;' method='post' >";
		tmp::head("<style>.conftable{} .conftable td{vertical-align:middle;}</style>");

		foreach($conf as $name=>$item) {
		    tmp::exec("/mod/conf/section",$name,$item);
		}

		echo "<br/><br/>";
	    echo "<input type='submit' value='Сохранить' />";
	    echo "<input type='hidden' name='cmd' value='mod:conf:controller:save' />";
	    echo "</form>";

	    tmp::script("$(function(){
	        $('.conf-help').click(function(){
	            var id = $(this).attr('infuso:id');
	            $('#help-'+id).toggle(200);
	        })
	    });");

		admin::footer();
	}
	
	public function index_components() {
	
	    $conf = file::get("/mod/conf/components.yml")->data();
	    tmp::exec("/mod/conf/components",array(
	        "conf" => $conf,
		));
	}

	public static function postTest() {
		return mod_superadmin::check();
	}

	public static function post_save($p) {
		unset($p["cmd"]);
		mod_conf::clearCache();
		mod::saveXMLConf(mod_conf::path(),$p);
		mod::fire("mod_confSaved");
	}
	
	public static function post_saveComponentsConf($p) {
		file::get("/mod/conf/components.yml")->put($p["conf"]);
	}
	
}
