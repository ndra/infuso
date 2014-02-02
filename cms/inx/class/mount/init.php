<?

/**
 * Класс для компипяции компонентов inx
 **/
class inx_init extends mod_init {

	public function priority() {
	    return 0;
	}

	public function init() {
	
	    mod::msg("inx");
	
		// Очищаем папку
		//file::get("/inx/pub/")->delete(true);

		foreach(mod::service("bundle")->all() as $mod) {
			self::buildModule($mod->path());
        }

	}
	
	public static function generateBuildID() {
		file::get(mod::app()->publicPath()."/inx/build_id.txt")->put(rand());
	}

	public static function packFile($mod,$file) {
		$path = self::getModulePath($mod)."/".$file;
		$file = inx_mount_file::get($path);
		$file->compileChain();
		self::generateBuildID();
	}

	public static function getModulePath($mod) {
		return mod::service("bundle")->bundle($mod)->inxPath();
	}

	public static function buildModule($mod) {
	
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
