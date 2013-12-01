<?

class update extends mod_controller {

	public function confDescription() {
	    return array(
	        "push" => array(
                "server" => "Сервер",
                "login" => "Логин",
                "password" => "Пароль",
                "path" => "Путь на сервере",
                "bundles" => "Список пакетов для закачивания",
			),
		);
	}

	public static function indexTest() { return mod_superadmin::check(); }
	public static function postTest() { return mod_superadmin::check(); }
	public static function indexTitle() { return "Архивация модулей"; }

	public static function index() {
		tmp::exec("admin:header",array("title"=>"Архивация модулей"));
		inx::add(array(
		    "type"=>"inx.mod.update.updater",
		));
		tmp::exec("admin:footer");
	}
	public static function indexFailed() { admin::fuckoff(); }

	public static function conf() {
        $ret = mod_conf::general("push");
        $ret["localPath"] = "/update/dump/";

        foreach(func_get_args() as $key) {
            $ret = $ret[$key];
        }
        return $ret;
	}
	
	

	/**
	 * push:
	 * server: dev.xxx.com
	 * login: mylogin
	 * password: qwerty
	 * path: ---
	 * bundles:
	 *   -admin
	 *   -board
	 *   ...
	 **/
	public static function post_upload() {
		set_time_limit(0);
		self::post_pack();

		$conn_id = ftp_connect(self::conf("server"));
		$login_result = ftp_login($conn_id,self::conf("login"),self::conf("password"));
		ftp_set_option($conn_id, FTP_TIMEOUT_SEC, 5);
		foreach(file::get(self::conf("localPath"))->dir() as $file) {
		    mod::trace("upload ".$file->name());
			$upload = ftp_put($conn_id,self::conf("path")."/".$file->name(), $file->native(), FTP_BINARY);
			if(!$upload) {
			    mod::trace("upload {$file->name()} failed");
				return "failed";
			}
		}
		return "done";
	}

	public static function post_pack() {

		file::get(self::conf("localPath"))->delete(true);
		file::mkdir(self::conf("localPath"));

        $bundles = self::conf("bundles");

		// Упаковываем все файлы
		foreach(mod::all() as $mod) {
		    if(in_array($mod,$bundles)) {
				self::packModule($mod);
			}
        }
	}

	/**
	 * Пакует модуль $mod в zip-архив и сохраняет в папку
	 **/
	public static function packModule($mod) {

		$public = mod::info($mod,"mod","leave");
		
		if(!$public) {
			$public = array();
		}
		
		if(!is_array($public)) {
			$public = array($public);
		}
		
		foreach($public as $key=>$val) {
		    $public[$key] = trim($val,"/");
		}

		$zip = new file_zip();
		// Удаляем все лишние файлы
		foreach(mod_file::get($mod)->dir() as $file) {
		    if($file->folder()) {
		   		if(!in_array($file->name(),$public))
					foreach($file->search() as $file) {
						$contents = !$file->folder() ? $file->data() : null;
						$zip->addFile($contents,$file->rel("/$mod/"));
					}
			} else {
				$contents = $file->data();
				$zip->addFile($contents,$file->rel("/$mod/"));
			}
		}

		file::get(self::conf("localPath")."/$mod.zip")->put($zip->file());
	}

}
