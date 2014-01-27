<? class inxdev_filemanager extends mod_controller {

public static function postTest() { return true; }
public static function post_list($p) {
	foreach(file::dir("/inxdev/filemanager/") as $file)
	    $ret[] = array(
	        "name" => $file->name(),
	        "preview" => $file->preview()->get(),
	        "big" => $file->preview(300,300)->get(),
		);
	return $ret;
}

public static function name($name) {
	$name = preg_replace("/[^.1234567890qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM-_]/","_",$name);
	$name = strtolower($name);
	return "/inxdev/filemanager/$name";
}

public static function post_upload($p,$files) {
	mod::msg($files);
}

public static function post_delete($p) {
	$name = self::name($p["name"]);
	file::get($name)->delete();
	mod::msg("Файл удален");
}

} ?>
