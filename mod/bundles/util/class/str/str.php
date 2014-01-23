<?

class util_str extends mod_component {

    private $str = "";

    public function __construct($str) {
        $this->str = $str;
    }

    public function __toString() {
        return $this->str."";
    }

    /**
     * Возвращает длину строки
     **/
    public function length() {
        return mb_strlen($this->str,"utf-8");
    }

    /**
     * Убирает пробельные символы с концов строки
     **/
    public function trim() {
        $this->str = trim($this->str);
        return $this;
    }

    /**
     * Переводит строку в нижний регистр
     **/
    public function lower() {
        $this->str = mb_strtolower($this->str,"utf-8");
        return $this;
    }

    /**
     * Переводит строку в верхний регистр
     **/
    public function upper() {
        $this->str = mb_strtoupper($this->str,"utf-8");
        return $this;
    }

    /**
     * Экранирует строку
     **/
    public function esc() {
        $this->str = htmlspecialchars($this,ENT_QUOTES);
        return $this;
    }

    /**
     * Убирает из строки небезопасные элементы типа скриптов и незакрытых тэгов
     **/
    public function secure() {
        $this->str = preg_replace("/\<\s*script.*/i","<span style='color:white;background:red;'>&lt;script&gt;</span>",$this->str);
        return $this;
    }

    /**
     * Переводит строку в utf-8 из любой другой кодировки (исходная кодировка опрееделяется автоматически)
     **/
    public function decode() {
        $this->str = util_str_encoder::encode($this->str);
        return $this;
    }

    /**
     * Обрезает текст после определенной длины и вставляет троеточие
     **/
    public function ellipsis($n,&$flag=null) {
    
        $flag = true;

        $ellipsis = "...";

        $words = explode(" ",$this->text()."");

        $word = $words[0];
        if(mb_strlen($word)>$n) {
            $this->str = mb_substr($word,0,$n,"utf-8").$ellipsis;
            return $this;
        }

        $ret = "";
        foreach($words as $word) {
            $ret.="$word ";
            if(mb_strlen($ret,"utf-8")>$n) {
                $this->str = trim($ret,' /.,-()<>').$ellipsis;
                return $this;
            }
        }

        $flag = false;
        $this->str = $ret;
        return $this;
    }

    /**
     * Подсвечивает в строке заданное слово
     **/
    public function hl($word) {
        $words = util::splitAndTrim($word," ");
        $words = implode("|",$words);
        $str = preg_replace("/$words/iu", "<span style='background:yellow;' >\\0</span>", $this."");
        return new self($str);
    }

    /**
     * Загружает html из строки
     * @return На выходе получается simplexml
     **/
    public function html() {
        $doc = new DOMDocument();
        @$doc->loadHTML("<head><meta http-equiv='Content-Type' content='text/html; charset=utf-8' /></head>".$this);
        $xml = simplexml_import_dom($doc);
        return $xml;
    }

    /**
     * Чинит html: закрывает незакрытые тэги
     **/
    public function repair() {
        $html = $this->html();
        $str = $html->body->asXML();
        $str = preg_replace("/^\<body\>/","",$str);
        $str = preg_replace("/\<\/body\>$/","",$str);
        return new self($str);
    }

    /**
     * Убирает тэги из текста
     * @return object util_string
     **/
    public function text() {
        $str = strip_tags($this);
        return new self($str);
    }

    /**
     * Удаляет из строки двойные пробелы
     **/
    public function removeDuplicateSpaces() {
        $s = preg_replace("/\s+/"," ",$this."");
        return new self($s);
    }

    /**
     * Подготавливает строку для поиска
     * Убирает лишние символы
     * Оставляет только корни слов
     * Дополняет все слова до пяти символов
     **/
    public function prepareForSearch() {

        $s = $this->stem()->lower();

        $s = preg_replace("/[\.\,\-\(\)\<\>\[\]]/"," ",$s);

        $ret = array();
        foreach(util::splitAndTrim($s," ") as $word) {
            $n = max(5 - mb_strlen($word),0);
            $word = str_repeat("ъ",$n).$word;
            $ret[] = $word;
        }

        return implode(" ",$ret);
    }
    
    public function removeBom() {
    
        $bom = pack("CCC", 0xef, 0xbb, 0xbf);
        $str = $this."";
        if (0 == strncmp($str, $bom, 3)) {
            $str = substr($str, 3);
        }
        
        return new self($str);
        
    }
    
    /**
     * Преобразует строку в транслит
     **/
    public function translit() {
        $tr = array(
            "й" => "y",
            "ц" => "ts",
            "у" => "u",
            "к" => "k",
            "е" => "e",
            "ё" => "e",
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
        
        $tr2 = array();
        foreach($tr as $key=>$val) {
            $tr2[$key] = $val;
            $tr2[util::str($key)->upper().""] = util::str($val)->upper()."";
        }
        
        return new self(strtr($this,$tr2));
    }
    
    /**
     * Находит в тексте ссылки и зменяет их тэгами <a>
     **/
    public function makeLinks() {
    
        $text = (string)$this;

        $text = preg_replace(
            "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/",
            '<a href="$0" >$0</a> ',
            $text);
            
        $text = preg_replace('/(\S+@\S+\.\S+)/', '<a href="mailto:$1">$1</a>', $text);

        return new self($text);

    }

}
