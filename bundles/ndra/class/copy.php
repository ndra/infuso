<?

/**
 * Класс выполняет единственную функцию - выводит наш копирайт.
 **/
class ndra_copy extends mod_controller {

    /**
    * Добавляет наш копирайт
    * Кэширует запросы к серверу
    **/
    public static function add() {

        $key = "ndra:copy".($_SERVER["REQUEST_URI"]=="/" ? "/root" : "");

        if(!$copy = mod_cache::get($key)) {

            $copy = file_get_contents("http://www.ndra.ru/dra/api/copyright/",false, stream_context_create(array('http' =>
                array (
                    "method" => "POST",
                    "header" => "Content-type: application/x-www-form-urlencoded",
                    "timeout" => 1,
                    "content" => http_build_query($_SERVER)
                ),
            )));

            if(!$copy) {
                $copy = " ";
            }

            mod_cache::set($key,$copy);
        }

        return $copy;

    }

}
