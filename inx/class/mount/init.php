<?

class inx_init extends mod_init {

	public function priority() {
	    return 0;
	}

	public function init() {
	
	    mod::msg("inx");
	
		// Очищаем папку
		file::get("/inx/pub/")->delete(true);
		foreach(mod::all() as $mod)
			self::buildModule($mod);
		self::generateBuildID();
	}

	public static function packFile($mod,$file) {
		$path = self::getModulePath($mod)."/".$file;
		$file = inx_mount_file::get($path);
		$file->compileChain();
		self::generateBuildID();
	}

	public static function getModulePath($mod) {
		$path = mod::info($mod,"inx","path");
	    return $path ? "/$mod/$path/" : null;
	}

	public static function generateBuildID() {
		file::get("/inx/build_id.txt")->put(rand());
	}

	public static function buildModule($mod) {
	
	    inx_mount_file::conf("pack",true);
		$path = self::getModulePath($mod);
		
		if(!$path) {
			return;
		}
		
		foreach(file::get($path)->search() as $file) {
	    	if(!$file->folder()) {
	    		inx_mount_file::get($file->path())->compile();
			}
		}
	}

}
