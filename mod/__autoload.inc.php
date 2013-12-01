<?



function __autoload($class) {

	$quickMap = array(
        "mod" => "/mod/class/mod.php",
        "mod_classmap" => "/mod/class/classmap/classmap.php",
        "mod_handler" => "/mod/class/handler.php",
        "mod_superadmin" => "/mod/class/superadmin.php",
        "mod_service" => "/mod/class/service.php",
        "mod_controller" => "/mod/class/controller/controller.php",
        "mod_component" => "/mod/class/component.php",
        "mod_file" => "/mod/class/file/file.php",
        "mod_file_filesystem" => "/mod/class/file/filesystem.php",
        "mod_file_http" => "/mod/class/file/http.php",
        "mod_file_list" => "/mod/class/file/list.php",
        "mod_crypt" => "/mod/class/crypt.php",
        "mod_post" => "/mod/class/post.php",
        "mod_event" => "/mod/class/event.php",
        "mod_url" => "/mod/class/url.php",
        "mod_action" => "/mod/class/action.php",
        "mod_bundle" => "/mod/class/bundle/bundle.php",
        "mod_console" => "/mod/class/console.php",
        "mod_conf" => "/mod/class/conf/conf.php",
        "mod_update" => "/mod/class/update.php",
        "mod_log" => "/mod/class/log/log.php",
        "mod_log_msg" => "/mod/class/log/msg.php",
		"mod_unzip" => "/mod/class/unzip.php",
		"mod_profiler" => "/mod/class/profiler.php",
		"mod_confLoader_xml" => "/mod/class/confLoader/xml.php",
		"mod_confLoader_yaml" => "/mod/class/confLoader/yaml.php",
        "mod_cmd" => "/mod/class/cmd.php",
        "mod_behaviour" => "/mod/class/behaviour.php",
        "mod_controller_behaviour" => "/mod/class/controller/behaviour.php",
        "mod_app" => "/mod/class/app.php",
	);
	
	if($path = $quickMap[$class]) {
	    include $_SERVER["DOCUMENT_ROOT"].$path;
	    return;
	}
	
	$map = mod::classmap("map");
	
	if($map[$class]) {
	    $path = $_SERVER["DOCUMENT_ROOT"].$map[$class]["f"];
	    $t = microtime(1);
	    include $path;
	    $GLOBALS["infusoClassTimer"] += microtime(1) - $t;
	}

}


