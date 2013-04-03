<?

class seotools_slowLoad extends reflex implements mod_handler {

    public static function get($url,$headers="") {
        return self::all()->eq("url",$url)->eq("headers",$headers)->one();
    }
    
    public static function all() {
        return reflex::get(get_class());
    }
    
    public static function load($url,$headers="") {
        $page = self::get($url,$headers);
        if(!$page->exists()) {
            $page = reflex::create(get_class(),array(
                "url" => $url,
                "headers" => $headers,
            ));
        }
        return $page;
    }
    
    public static function loadOne() {
        $page = self::all()->asc("loaded")->lt("loaded",util::now()->shift(-3600*24))->one();
        $page->doLoad();
    }
    
    public function reflex_beforeCreate() {
        // Указываем что страница загружена 10 лет назад
        $this->data("loaded",util::now()->shift(-3600*24*365*10));
    }
    
    /**
     * Загружает содержимое
     **/
    private function doLoad() {
    
        if(!$this->exists())
            return;
            
        $opts = array(
            "http"=>array(
                "method" =>"GET",
                "header" => $this->data("headers"),
            ),
        );    
                
        $context = stream_context_create($opts);  
            
        $contents = file_get_contents($this->data("url"),false,$context);
        
        $this->data("content",$contents);
        $this->data("loaded",util::now());
    }
    
    public function on_mod_cron() {
        self::loadOne();
    }

}
