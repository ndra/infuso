<?

namespace infuso\core;

class log {

    /**
     * Отправляет сообщение
     **/
    public function msg($message,$error=false,$extra=null) {
    
        $message = self::toString($message);

        @session_start();
        
        if(!$_SESSION["log:messages"]) {
            $_SESSION["log:messages"] = array();
        }
            
        $key = sizeof($_SESSION["log:messages"])-1;
        
        if($_SESSION["log:messages"][$key]["text"]==$message && $session[$key]["error"]==$error) {
            $_SESSION["log:messages"][$key]["count"]++;
        } else {
            $_SESSION["log:messages"][] = array(
                'text' => $message,
                'error' => $error,
                'extra' => $extra,
                "count" => 1,
            );
        }
    }

    /**
     * Очищает список сообщений
     **/
    public static function clear() {
        @session_start();
        $_SESSION["log:messages"] = array();
    }

    /**
     * Возвращает сообщения
     * Если параметр опущен или равен true, функция дополнительно очищает список сообщений.
     **/
    public static function messages($clear=true) {
        $msg = array();
        @session_start();
        if(!$_SESSION["log:messages"]) $_SESSION["log:messages"] = array();
        foreach($_SESSION["log:messages"] as $m) {
            $msg[] = new \mod_log_msg($m);
        }
        if($clear)
            self::clear();
        return $msg;
    }

    public function toString($a) {

        // Массив
        if(is_array($a)) {
            $a = var_export($a,1);
            $a = preg_replace("/\n/"," ",$a);
            return $a;
        }

        if(is_object($a)) {
            if(get_class($a)=="SimpleXMLElement") {
                return strtr(util::prettyPrintXML($a),array("\n"=>" "));
            }
        }

        // Скаляр или прочее
        return $a;
    }

    /**
     * Заносит запись в лог
     **/
    public static function trace($trace) {
    
        $tracePath = mod::app()->varPath()."/trace/";

        $trace = self::toString($trace);
        file::mkdir($tracePath);

        $message = "";
        $message.= date("h:i:s")." ";
        $debug = debug_backtrace();

        // Добавляем в трэйс метод из которого была сделана запись
        // Т.к. методы вызывают друг-друга, мы бедем последний по debug_backtrace(),
        // исключив из него вызовы внутри самого лога

        $skip = array("mod_log::trace","mod::trace"); // это мы пропустим

        foreach($debug as $d) {

            $call = $d["class"].$d["type"].$d["function"];

            if(!in_array($call,$skip)) {
                $message.= $call;
                break;
            }

        }

        $message.= " >> ".$trace."\n";
        $date = date("Y-m-d");
        $path = "/{$tracePath}/$date.txt";
        $handle = fopen(file::get($path)->native(),'a');
        fwrite($handle, $message);
    }

}
