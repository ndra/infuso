<?

/**
 * Контроллер для управления inx-компонентами
 **/
class moduleManager_inxManager extends mod_controller {

    public static function postTest() {
        return mod_superadmin::check();
    }
    
    private static function getPath($mod){
        return $mod."/".mod::info($mod,"inx","path");
    }
    
    public static function post_listItems($params) {
    
        $dir = self::getPath($params["module"])."/".$params["path"];
        $files = file::get($dir)->dir();
        $data = array();
        foreach($files as $file) {
            $text = $file->name();
            if($file->ext()=="js" | $file->folder()) {
                $text = explode(".",$text);
                $text = @$text[0];
                $data[] = $text;
            }
        }
        $data = array_unique($data);
    
        $ret = array();
        foreach($data as $item) {
        
            $file = inx_mount_file::get("$dir/$item.js");
            $linked = $file->isDirective("link_with_parent");
            
            $text = $item;
            if($c=null)
                $text.= "<i style='opacity:.5;' >".$c."</i>";
            
            $ret[] = array(
                "icon" => $linked ? "/moduleManager/res/inx_linked.png" : "/moduleManager/res/inx.gif",
                "text" => $text,
                "linked" => $linked,
                "folder"=> !!file::get("/$dir/$item/")->dir()->count(),
                "editable" => true
            );
        }
        
        usort($ret,array("self","sort"));
        return $ret;
    }
    
    private static function sort($a,$b) {
        if($a["linked"]) return -1;
        if($b["linked"]) return 1;
        return strcmp($a["text"],$b["text"]);
    }
    
    public static function post_getContents($params) {
        $path = self::getPath($params["module"]);
        $php = file::get($path."/".$params["path"].".js")->data();
        return array(php=>$php);
    }
    
    public static function post_setContents($params) {
    
        // Записываем код компонента в файл
        $path = self::getPath($params["module"]);
        file::get($path."/".$params["path"].".js")->put($params["code"]);
        
        // Компилируем компонет
        inx_init::packFile($params["module"],$params["path"].".js");
        mod::msg("Компонент сохранен");
    }
    
    
    public static function post_newComponent($p) {
        $dir = self::getPath($p["module"])."/".$p["path"];
        file::mkdir($dir);
        for($i=1;$i<100;$i++) {
            $path = "$dir/new$i.js";
            if(file::get($path)->exists())
                continue;
            file::get($path)->put("// Новый компонент");
            break;
        }
    }
    
    public static function post_deleteComponent($p) {
        $dir = self::getPath($p["module"])."/".$p["path"].".js";
        file::get($dir)->delete(true);
        $dir = self::getPath($p["module"])."/".$p["path"];
        file::get($dir)->delete(true);
    }
    
    public static function post_renameComponent($p) {
    
        $module = $p["module"];
        $path = $p["old"];
        $new_name = $p["new"];
    
        $old = self::getPath($module)."/".$path.".js";
        $new = self::getPath($module)."/".$new_name.".js";
        file::get($old)->rename($new);
    
        $old = self::getPath($module)."/".$path;
        $new = self::getPath($module)."/".$new_name;
        file::get($old)->rename($new);
    
        mod::msg("Компонент переименован");
    
    }
    
}
