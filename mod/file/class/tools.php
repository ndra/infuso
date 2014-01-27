<?

/**
 * Контроллер управления превьюшками
 **/
class file_tools extends mod_controller{

public static function indexTest(){
	return mod_superadmin::check();
}

public static function indexFailed(){
	return admin::fuckoff();
}

public static function postTest(){
	return mod_superadmin::check();
}

public static function index() {
    admin::header("Очистка превьюшек");
    echo "<div style='padding:40px;' >";
    
	inx::add(array(
	    "type" => "inx.mod.file.previews",
	));
    
    echo "</div>";
    admin::footer();
}

/**
 * Экшн сбора информации и очистки превьюшек
 **/
public function post_collectPreviews($p) {
	$folder = $p["folder"];
	
	if(!$folder)
		$folder = "/file/preview";
	
	if(!preg_match("/^\/file\/preview/",$folder))
	    return "done";
	    
	$folder = file::get($folder);
	
	if($p["clear"])
	    foreach($folder->dir() as $file)
	        $file->delete();

	return array(
	    "files" => $folder->dir()->files()->count(),
	    "size" => $folder->dir()->files()->size(),
		"folder" => $folder->walk()."",
		"clear" => $p["clear"],
	);
	
}

}
