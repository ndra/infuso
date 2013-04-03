<? class mod_update {

    public static function loadXMLConf($doc) {

        if(is_string($doc))$doc = @simplexml_load_string(file::get($doc)->data());
        if(!$doc)
            return false;

        $ret = array();
        foreach($doc->children() as $child)
            if($child->getName()=="param")
                $ret[$child->attributes()->name.""] = trim($child."");
            elseif($child->getName()=="set")
                $ret[$child->attributes()->name.""] = self::loadXMLConf($child);
        return $ret;
    }

    // Проверяет обновление данного модуля на сервере
    // В случае, если обновление обноружено, возвращает ссылку на файл для скачивания
    public static function search($mod) {
        $url = mod::conf("mod:updateURL");
        $descr = self::loadXMLConf(simplexml_load_file($url));
        if(!$descr) return false;
        return @$descr[$mod]["download"];
    }
    
    // Обновляет модуль $mod
    // Скачивает архив с вервера обновления, удаляет старые файлы модуля и распаковывает скачанный архив
    public static function update($mod) {

        $url = self::search($mod);
        if(!$url) {
            mod_log::msg("Модуль $mod не найден на сервере",1);
            return false;
        }
        
        mod_file::http($url)->copy("1.zip");
        $zip = mod_file::get("1.zip");
        if(!$zip->exists()) {
            mod_log::msg("Ошибка скачивания файла $url");
            return false;
        }

        // Распоковываем модуль во временную папку
        mod_file::get("/__tmp/")->delete(true);
        $zip->unzip("/__tmp/");
        $zip->delete();
        
        // Определяем какие файлы модуля нужно сохранить
        $ini = @parse_ini_file(mod_file::get("/__tmp/info.ini")->native(),1);
        if(is_array($ini)) {
            $leave = $ini["mod"]["leave"];
            if(!$leave) $leave = array();
            if(!is_array($leave)) $leave = array($leave);
            foreach($leave as $key=>$val)
                $leave[$key] = trim($val,"/");

            // Удаляем все лишние файлы
            foreach(mod_file::get($mod)->dir() as $file)
                if(!in_array($file->name(),$leave) | !$file->folder())
                    $file->delete(true);

            // Копируем временную папку, удаляем ее
            mod_file::get("/__tmp/")->copy($mod);
            mod_file::get("/__tmp/")->delete(true);

            mod_log::msg("Модуль $mod обновлен");
        } else {
            mod_log::msg("$mod - Ошибка чтения mod.ini",1);
        }
        
    }

}
