<?

class moduleManager_fileManager extends mod_controller {

    public static function postTest() {
        return mod_superadmin::check();
    }

    /**
     * Возвращает список файлов
     **/
    public static function post_listFiles($params) {

        $dir = $params["module"]."/".$params["path"];
        $files = file::get($dir)->dir()->sort();
        foreach($files as $item) {
            $file = array(
                "text"=>$item->name(),
                "folder"=>!!$item->dir()->count(),
                "dir" => $item->folder(),
                "editable" => true,
                "preview" => $item->preview()->resize()->get(),
                "path" => $item->rel($params["module"])
            );

            if(!$item->folder()) {
                $ext = $item->ext();
                $icon = in_array($ext,array("php","js")) ? $ext : "page";
                $file["icon"] = "/moduleManager/icons/$icon.gif";
                if(in_array($ext,array("jpg","gif","png"))) {
                    $file["icon"] = $item->preview(16,16)."";
                }
            } else {
                $file["icon"] = "folder";
            }
            $ret[] = $file;
        }
        return $ret;
    }

    /**
     * Возвращает список файлов, полученных на одной итерации поиска
     **/
    public static function post_searchFiles($params) {

        $dir = $params["path"];
        $files = file::get($dir)->dir();
        foreach($files as $item) {
            $file = array(
                "text"=> $item->name(),
                "path" => $item->rel($params["module"])
            );
        }
        return $ret;
    }

    /**
     * Возвращает содержимое файла
     **/
    public static function post_getContents($params) {

        $path = $params["module"]."/".$params["path"];
        if(!file::get($path)->folder()) {
            $ext = strtolower(file::get($path)->ext());
            switch($ext) {
                case "php":
                case "js":
                case "ini":
                   // mod_cmd::meta("type","code");
                    return array(
						"code" => file::get($path)->data(),
						"lang" => $ext,
						"type" => "code",
					);
                    break;

                default:
                    //mod_cmd::meta(type,"code");
                    return array(
						"code" => file::get($path)->data(),
						"lang" => "text",
						"type" => "code",
					);
                    break;

                case "gif":
                case "jpg":
                case "jpeg":
                    return array(
						"type" => "img",
					);
                    //mod_cmd::meta(type,"img");
                    break;
            }
        } else {
            return array(
				//"code" => file::get($path)->data(),
				//"lang" => "text",
				"type" => "folder",
			);
        }
    }

    public static function post_setContents($params) {
        $path = $params["module"]."/".$params["path"];
        $file = file::get($path);

        if(!$file->exists()) {
            mod::msg("Файл, который вы пытаетесь сохранить не существует",1);
            return;
        }

        $file->put($params["php"]);

        if($file->data()==$params["php"])
            mod::msg("Файл сохранен");
        else
            mod::msg("Не удалось сохранить файл",1);
    }

    public static function post_newFile($p) {
        $dir = $p["module"]."/".$p["path"];
        file::mkdir($dir);
        for($i=1;$i<100;$i++) {
            $path = "$dir/new$i.php";
            if(file::get($path)->exists())
                continue;
            file::get($path)->put("<"."? ?".">");
            break;
        }
        return $p["path"];
    }

    public static function post_newFolder($p) {
        $dir = $p["module"]."/".$p["path"];
        @file::mkdir($dir);
        for($i=1;$i<100;$i++) {
            $path = "$dir/new$i";
            if(file::get($path)->exists())
                continue;
            file::mkdir($path);
            break;
        }
        return $p["path"];
    }

    public static function post_deleteFiles($p) {

        if(!$p["module"]) return;
        foreach($p["files"] as $file) {
            if(trim($file,"/"))
                file::get("/$p[module]/$file")->delete(true);
        }
        return file::get($file)->up()->path();
    }

    public static function post_renameFile($p) {

        $module = $p["module"];
        $path = $p["old"];
        $new_name = $p["new"];

        $old = $module."/".$path;
        $new = $module."/".$new_name;
        file::get($old)->rename($new);

        mod::msg("Файл переименован");
        return file::get($p["old"])->up()->path();
    }

    public static function post_upload($p,$files) {
        $file = $files["file"];
        file::moveUploaded($files["file"]["tmp_name"],$p["module"]."/".$p["path"]."/$file[name]");
        return $p["path"];
    }

	/**
	 * Контроллер упаковки выбранных файлов в zip
	 **/
    public static function post_pack($p) {
    
        $dest = "/moduleManager/pack/";
        file::get($dest)->delete(1);
        file::mkdir($dest);

        foreach($p["files"] as $file)
            file::get("$p[module]/$file")->copy("$dest/".file::get($file)->name());

        $id = strtr(util::now()->num(),array(" "=>"---",":"=>"-","."=>"-"));
        $zip = $dest.$id.".zip";
        file::get($dest)->zip($zip);

        return $zip;
    }

}
