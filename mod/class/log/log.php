<?

class mod_log {

    /**
     * Отправляет сообщение
     **/
    public function msg($message,$error=false,$extra=null) {
    
        if(is_array($message))
            $message = var_export($message,1);
        @session_start();
        
        if(!$_SESSION["log:messages"])
            $_SESSION["log:messages"] = array();
            
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
            $msg[] = new mod_log_msg($m);
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
            if(get_class($a)=="SimpleXMLElement")
                return strtr(util::prettyPrintXML($a),array("\n"=>" "));
        }

        // Скаляр или прочее
        return $a;
    }

    public static function trace($trace) {
        $trace = self::toString($trace);
        file::mkdir("/mod/trace/");

        $message = "";
        $message.= date("h:i:s")." ";
        $debug = debug_backtrace();
        $message.= $debug[1]["class"]."::".$debug[1]["function"]." ";
        $message = str_pad($message,100,"-")."\n";

        $message.= $trace."\n";
        $date = date("Y-m-d");
        $path = "/mod/trace/$date.txt";
        $handle = fopen(file::get($path)->native(),'a');
        fwrite($handle, $message);
    }

}
