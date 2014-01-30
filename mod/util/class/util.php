<?

namespace infuso\util;
use \infuso\core\file;

/**
 * Класс с набором статических методов, облегчающий рутинные действия
 **/
class util {

    /**
     * Выводит профайлер
     **/
    public static function profiler() {
        \util_profiler::profiler();
    }

    /**
     * Выводит на экран стэк функций в читаемом формате
     **/
    public static function backtrace() {

        echo "<div style='padding:10px;border:1px solid #ccc;' >";

        foreach(debug_backtrace() as $item) {
            echo "<b>";

            echo $item["class"];
            if($item["object"])
                if(method_exists($item["object"],"id"))
                    echo "(".$item["object"]->id().")";

            echo "::";
            echo $item["function"];
            echo "</b>";
            $args = array();
            foreach($item["args"] as $arg) {
                switch(gettype($arg)) {
                    case "object":
                        $args[] = "<i>(obj)".get_class($arg)."</i>";
                        break;
                    default:
                        $args[] = $arg."";
                        break;
                }
            }
            echo " (".implode(",",$args).")";
            echo "<br/>";
        }

        echo "</div>";
    }

    /**
     * Выводит XML елочкой в строку
     **/
    public static function prettyPrintXML($xml,$root=1) {

        if(get_class($xml)=="SimpleXMLElement")
            $xml = dom_import_simplexml($xml);


        $ret = array();
        switch($xml->nodeType) {
            case 9:
                $ret = self::prettyPrintXML($xml->firstChild,0);
                break;
            case 1:
                $start = '<'.$xml->nodeName;
                $attr = array();
                foreach($xml->attributes as $attribute)
                    $attr[] = $attribute->nodeName."="."'".htmlspecialchars($attribute->nodeValue)."'";
                $start.= sizeof($attr) ? " ".implode(" ",$attr)." " : "";
                $start.=">";

                if($xml->childNodes->length==1 & $xml->firstChild->nodeType==3) {
                    $ret[] = $start.htmlspecialchars($xml->firstChild->nodeValue).'</'.$xml->nodeName.'>';
                }
                else {
                    $ret[] = $start;
                    foreach($xml->childNodes as $child)
                        foreach(self::prettyPrintXML($child,0) as $str)
                            $ret[] = "\t".$str;
                    $ret[] = '</'.$xml->nodeName.'>';
                }
                break;
            case 3:
                if(trim($xml->nodeValue))
                    $ret[] = htmlspecialchars(trim($xml->nodeValue));
                break;
        }

        if(!$root) return $ret;
        else return implode("\n",$ret);
    }

    /**
     * Загружает xml с настройкаим и делает из него строку
     **/
    public static function loadXMLConf($doc) {

        $begin = false;
        if(is_string($doc)) {
            mod_profiler::beginOperation("util","loadXMLConf",$doc);
            $begin = true;
            $doc = @simplexml_load_string(file::get($doc)->data());
        }

        if(!$doc) {
            mod_profiler::endOperation();
            return false;
        }

        $ret = array();
        foreach($doc->children() as $child)
            if($child->getName()=="param")
                $ret[$child->attributes()->name.""] = trim($child."");
            elseif($child->getName()=="set")
                $ret[$child->attributes()->name.""] = self::loadXMLConf($child);

        if($begin) {
            mod_profiler::endOperation();
            $doc = @simplexml_load_string(file::get($doc)->data());
        }
                
        return $ret;
        
    }

    public static function saveXMLConf($target,$conf) {

        if(is_string($target)) {
            $path = $target;
            $target = simplexml_load_string("<set></set>");
        }

        foreach($conf as $name=>$item)
            if(is_array($item))    {
                $set = $target->addChild("set");
                $set->addAttribute("name",$name);
                self::saveXMLConf($set,$item);
            } else {
                $param = $target->addChild("param",htmlspecialchars($item));
                $param->addAttribute("name",$name);
            }

        if(@$path)
            file::get($path)->put(self::prettyPrintXML(dom_import_simplexml($target)));
    }

    // Сохраняет массив в файл для подключения
    public static function save_for_inclusion($path,$data) {
        $data = "<"."? return ".var_export($data,1)."; ?".">";
        file::get($path)->put($data);
    }

    /**
     * Генерирует случайную строку из букв и цифр символов заданой длины (по умолчанию - 30)
     **/
    public static function id($length=30) {
        $chars = "1234567890qwertyuiopasdfghjklzxcvbnm";
        $ret = "";
        for($i=0;$i<$length;$i++)
            $ret.= $chars[rand()%strlen($chars)];
        return $ret;
    }

    public static function get_prefixed_items($src,$prefix)
    {
        $ret = array();
        foreach($src as $key=>$val)
        {
            if(substr($key,0,strlen($prefix))==$prefix)
            {
                $name = substr($key,strlen($prefix));
                $ret[$name]=$val;
            }
        }
        return $ret;
    }

    public static function filter($array,$filter) {
        $filter = is_array($filter) ? $filter : explode(",",$filter);
        $ret = array();
        foreach($filter as $item) {
            $item = trim($item);
            //if($val=trim($array[$item]))
            $val=trim($array[$item]);
            $ret[$item] = $val;
        }
        return $ret;
    }

    public function filterArrayByKeyPrefix($array,$prefix) {
        $ret = array();
        foreach($array as $key=>$val) {
            if(substr($key,0,strlen($prefix))==$prefix)
                $ret[substr($key,strlen($prefix))] = $val;
        }
        return $ret;
    }

    public static function splitAndTrim($str,$separator) {
        $ret = array();
        foreach(explode($separator,$str) as $part) {
            if(trim($part)!=="") {
                $ret[] = $part;
            }
        }
        return $ret;
    }

    /**
     * Возвращает текущую дату
     **/
    public static function now() {
        return \util_date::now();
    }

    /**
     * Алиас к util_date::get()
     **/
    public static function date() {
        $args = func_get_args();
        return call_user_func_array(array("util_date","get"),$args);
    }

    /**
     * @return class util_string
     */
    public static function str($str) {
        return new \util_str($str);
    }
    
    /**
     * @return class util_string
     */
    public static function a($a) {
        return new \util_array($a);
    }

    /**
     * @return Возвращает стоимость в денежном формате - с ддвумя знаками после запятой и пробелами в цифрах
     */
    public static function price($price) {
        return number_format($price,2,".",$price>9999 ? " " : "");
    }
    
    /**
     * @return Возвращает стоимость в денежном формате - с ддвумя знаками после запятой и пробелами в цифрах
     * Копейки не выводятся
     */
    public static function intprice($price) {
        return number_format($price,0,".",$price>9999 ? " " : "");
    }

    public static function translit($str) {
        $tr = array(
            "й" => "y",
            "ц" => "ts",
            "у" => "u",
            "к" => "k",
            "е" => "e",
            "н" => "n",
            "г" => "g",
            "ш" => "sh",
            "щ" => "sh",
            "з" => "z",
            "х" => "h",
            "ъ" => "",
            "ф" => "f",
            "ы" => "i",
            "в" => "v",
            "а" => "a",
            "п" => "p",
            "р" => "r",
            "о" => "o",
            "л" => "l",
            "д" => "d",
            "ж" => "zh",
            "э" => "e",
            "я" => "ya",
            "ч" => "ch",
            "с" => "s",
            "м" => "m",
            "и" => "i",
            "т" => "t",
            "ь" => "",
            "б" => "b",
            "ю" => "yu",
        );
        $str = strtr($str,$tr);
        return $str;
    }

    public static function jsonEncode($data) {
        $arr_replace_utf = array('\u0410', '\u0430','\u0411','\u0431','\u0412','\u0432',
        '\u0413','\u0433','\u0414','\u0434','\u0415','\u0435','\u0401','\u0451','\u0416',
        '\u0436','\u0417','\u0437','\u0418','\u0438','\u0419','\u0439','\u041a','\u043a',
        '\u041b','\u043b','\u041c','\u043c','\u041d','\u043d','\u041e','\u043e','\u041f',
        '\u043f','\u0420','\u0440','\u0421','\u0441','\u0422','\u0442','\u0423','\u0443',
        '\u0424','\u0444','\u0425','\u0445','\u0426','\u0446','\u0427','\u0447','\u0428',
        '\u0448','\u0429','\u0449','\u042a','\u044a','\u042d','\u044b','\u042c','\u044c',
        '\u042d','\u044d','\u042e','\u044e','\u042f','\u044f');
        $arr_replace_cyr = array('А', 'а', 'Б', 'б', 'В', 'в', 'Г', 'г', 'Д', 'д', 'Е', 'е',
        'Ё', 'ё', 'Ж','ж','З','з','И','и','Й','й','К','к','Л','л','М','м','Н','н','О','о',
        'П','п','Р','р','С','с','Т','т','У','у','Ф','ф','Х','х','Ц','ц','Ч','ч','Ш','ш',
        'Щ','щ','Ъ','ъ','Ы','ы','Ь','ь','Э','э','Ю','ю','Я','я');
        $str1 = json_encode($data);
        $str2 = str_replace($arr_replace_utf,$arr_replace_cyr,$str1);
        return $str2;
    }
    
    function bytesToSize1000($bytes, $precision = 2) {
    
        // human readable format -- powers of 1000
        //
        $unit = array('b','kb','mb','gb','tb','pb','eb');
    
        return @round(
            $bytes / pow(1000, ($i = floor(log($bytes, 1000)))), $precision
        ).' '.$unit[$i];
    }

}
