<?

namespace infuso\core;

class url {

    private $scheme = null;
    private $host = null;
    private $path = null;
    private $port = null;
    private $query = array();
    private $hash = null;

    public function __construct($url) {

        // Составляем режек для url
        // Мы не используем parse_url(), т.к. хочется полного контроля над разбором
        // К примеру, parse_url() возвращает false, если в адресе есть двоеточие ":"
        // Это противоречит стандарту, но, нем не менее, используется на некоторых сайтах

        $urlSymbols = "\w\/\-\:\.\=\&\%\+\*";

        $scheme  = "^(?:(?P<scheme>\w+):(//)?)";
        $login  = "(?:(?P<login>\w+):(?P<pass>\w+)@)?";
        $host = "(?P<host>[\w\.\-]+)";
        
        $port = "(?::(?P<port>\d+))?";
        $path = "(?P<path>[{$urlSymbols}]*)?";
        $query = "(?:\?(?P<query>[{$urlSymbols}]*))?";
        $hash = "(?:#(?P<hash>.+))?";
        
        $r = "!($scheme$login$host)?$port$path$query$hash$!u";

        preg_match ($r, $url, $matches);

        $this->scheme = $matches["scheme"];
        $this->host = $matches["host"];
        $this->port = $matches["port"];
        $this->path = urldecode($matches["path"]);
        parse_str($matches["query"],$query);
        $this->query = $query;
        $this->hash = $matches["hash"];
    }

    /**
     * Конструктор
     **/
    public static function get($url) {
        return new self($url);
    }

    /**
     * Приведение к строке
     **/
    public function __tostring() {
        return $this->url();
    }

    /**
     * Создает новый объект на основе текущего урл
     **/
    public function current() {
        return mod::app()->url();
    }

    /**
     * Возвращает / менятет путь
     * Для http://www.mysite.ru/index.php?=123 путь будет /index.php
     * Путь всегда возвращается без закрывающего слэша
     **/
    public function path($path=null) {

        if(func_num_args()==0) {
            $ret = $this->path;
            if($ret!="/")
                $ret = rtrim($ret,"/");
            return $ret;
        }

        if(func_num_args()==1) {
            $this->path = $path;
            return $this;
        }

    }

    /**
     * Без параметров - возвращает хост
     * С одним параметром - меняет хост
     **/
    public function host($host=null) {

        if(func_num_args()==0) {
            return $this->host;
        }

        if(func_num_args()==1) {
            $this->host = $host;
            return $this;
        }

    }
    
    /**
     * Без параметров - возвращает порт
     * С одним параметром - меняет порт
     **/
    public function port($host=null) {

        if(func_num_args()==0) {
            return $this->port;
        }

        if(func_num_args()==1) {
            $this->port = $port;
            return $this;
        }

    }

    /**
     * Без параметров - возвращает хэш
     * С одним параметром - меняет хэш
     **/
    public function hash($hash=null) {

        if(func_num_args()==0) {
            return $this->hash;
        }

        if(func_num_args()==1) {
            $this->hash = $hash;
            return $this;
        }

    }

    /**
     * Алиас к host
     **/
    public function server() {
        $a = func_get_args();
        return call_user_func_array(array($this,"host"),$a);
    }

    /**
     * Алиас к host
     **/
    public function domain() {
        $a = func_get_args();
        return call_user_func_array(array($this,"host"),$a);
    }

    /**
     * Возвращает домен без www
     **/
    public function domainWithoutWWW($host=null) {
        return strtr($this->host(),array("www."=>""));
    }

    /**
     * Без параметров - возвращает схему
     * С одним параметром - меняет схему
     **/
    public function scheme($scheme=null) {

        if(func_num_args()==0) {
            return $this->scheme;
        }

        if(func_num_args()==1) {
            $this->scheme = $scheme;
            return $this;
        }

    }

    /**
     * Без параметров - возвращает массив параметров запроса
     * С одним параметром - возаращает параметр запроса
     * Два параметра - меняет параметр строки запроса
     **/
    public function query($key=null,$val=null) {

        if(func_num_args()==0) {
            return $this->query;
        }

        if(func_num_args()==1) {
            return $this->query[$key];
        }

        if(func_num_args()==2) {
            $this->query[$key] = $val;

            if($val===null || $val===false)
                unset ($this->query[$key]);

            return $this;
        }

    }

    /**
     * Возвращает строку запроса
     **/
    public function queryString() {
        return http_build_query($this->query());
    }
    
    public function trailingBackslash() {
    
        if(preg_match("/\.[0-9a-z]+$/",$this->path())) {
            return false;
        }
        
        if(!trim($this->path(),"/")) {
			return false;
        }
        
        return true;
    }

    /**
     * Возвращает полный url
     **/
    public function url() {

        $ret = "";
        if($this->scheme()) {
            $ret.= $this->scheme()."://";
        }

        if($this->host()) {
            $ret.= $this->host();
        }
        
        if($this->port()) {
            $ret.= ":".$this->port();
        }

        if($this->path()) {
            $ret.= $this->path();
        }
        
        if($ret!="/") {
        	$ret = rtrim($ret,"/");
        }
        
		if($this->trailingBackslash()) {
		    $ret .= "/";
		}

        if($this->queryString()) {
            $ret.= "?".$this->queryString();
        }

        if($this->hash()) {
            $ret.= "#".$this->hash();
        }

        return $ret;
    }

    /**
     * Возвращает относительный путь
     * Алиас к relative()
     **/
    public function relativeURL() {
        return $this->relative();
    }

    /**
     * Возвращает относительный путь (все что после домена)
     **/
    public function relative() {
        $ret = "";
        if($this->path())
            $ret.= $this->path();
        if($this->queryString())
            $ret.= "?".$this->queryString();
        return $ret;
    }

    public function absolute() {
        if(!$this->scheme()) {
            $this->scheme(mod_url::current()->scheme());
        }
        if(!$this->host()) {
            $this->host(mod_url::current()->host());
        }
        return $this;
    }

    /**
     * Взвращает экшн для данного урл
     **/
    public function action() {
        return mod::service("route")->urlToAction($this);
    }

}
