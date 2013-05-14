<?

/**
 * Класс гугл перевода
 **/
class google_translate extends mod_service {

    public function defaultService() {
        return "translate";
    }

    /**
     * Возвращает перевод текста
     **/
    public function translate($original,$source,$target) {

        $original = trim($original);

        // Не переводим пустые строки
        if(!$original)
            return "";

        // Не переводим числа
        if(is_numeric($original)) {
            return $original;
        }

        mod_profiler::beginOperation("google translate","cached",$original);

        $key = $original."-".$source."-".$target;

        // Пытаемся взять перевод из кэша
        if($cached = mod_cache::get($key)) {
            mod_profiler::endOperation();
            return $cached;
        }

           // Пытаемся достать перевод из базы
        $item = google_translate_cache::all()->eq("original",$original)->eq("source",$source)->eq("target",$target)->one();

           // Если ничего не досталось - делаем запрос в гугл
        if(!$item->exists()) {

            mod_profiler::updateOperation("google translate","real",$original);

            if($translation = self::request($original,$source,$target)) {

                $item = reflex::create("google_translate_cache",array(
                    "original" => $original,
                    "translation" => $translation,
                    "source" => $source,
                    "target" => $target,
                ));
                mod_cache::set($key,$item->data("translation"));

            } else {
                $item->data("translation",$original);
            }
        }

        mod_profiler::endOperation();

        return $item->data("translation");
    }

    /**
     * Выполняет запрос к translate api
     **/
    private static function request($str,$source,$target) {

        // Не делаем запрос, если длина переводимого слова один символ
        if(strlen($str)==1) {
            return $str;
        }

        $params = array(
            "key" => mod::conf("google:key"),
            "q" => $str,
            "source" => $source,
            "target" => $target,
        );

        $url = "https://www.googleapis.com/language/translate/v2?".http_build_query($params);

        $file = file::http($url);
        $tr = $file->data();

        if(!$tr) {
            throw new Exception("Google Translate request error: ".$file->errorText());
        }

        $tr = json_decode($tr,1);

        // Если при переводе возникла ошибка - выкидываем экзепшн
        if($tr["error"]) {
            $error = $tr["error"]["errors"][0];
            throw new Exception("Google translate error: {$error[message]} ({$error[reason]})");
        }

        return $tr["data"]["translations"][0]["translatedText"];
    }

}
