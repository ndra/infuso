<?

class seo_domain extends reflex {

    public static function all() {
        return reflex::get(get_class());
    }
    
    public static function get($id) {
        return reflex::get(get_class(),$id);
    }
    

    public static function reflex_root() {
        return array(
            self::all()->title("Домены")->param("tab","system")
        );
    }
    
    public function reflex_children() {
        return array(
            $this->queries()->title("Запросы")->param("sort",true)
        );
    }

    public function reflex_title() {
        return $this->data("domain");
    }

    public function queries() {
        return seo_query::all()->eq("domain",$this->id());
    }
    
    public function engines() {
        return $this->pdata("engines");
    }

    public function primaryEngine() {
        foreach($this->engines() as $engine) {
            return $engine;
        }
    }

    public function queriesInTop() {
        $engine = $this->primaryEngine();
        foreach($this->queries()->limit(0) as $query) {
            $position = $query->positions()->eq("date",util::now()->notime())->eq("engineID",$engine->id())->one()->data("position");
            if($position>=1 && $position<=10)
                $ids[] = $query->id();
        }
        return $this->queries()->eq("id",$ids);
    }

    public function extra($key,$val=null) {
        $extra = $this->pdata("extra");
        if(func_num_args()==1) {
            return $extra[$key];
        }
        if(func_num_args()==2) {
            $extra[$key] = $val;
            $this->data("extra",json_encode($extra));
        }
    }

    public static function scanOne() {
        self::all()->eq("backlinks",1)->asc("updated")->one()->scan();
    }

    public function scan() {
        if(!$this->exists()) return;
        $p = intval($this->extra("solomono"));
        if(!$p) $p = 1;
        $p = self::solomono($this->data("domain"),$p);
        $this->extra("solomono",$p);
        $this->data("updated",util::now());
    }

    public function solomono($requestDomain,$page=1) {
        $url = "http://solomono.ru/?search=$requestDomain&p=$page";
        $opts = array(
            'http' => array(
                'method'=> "GET",
                'header'=> "User-Agent:Mozilla/5.0 (Windows NT 5.1) AppleWebKit/534.30 (KHTML, like Gecko) Chrome/12.0.742.122 Safari/534.30",
            )
        );
        $context = stream_context_create($opts);
        $str = file_get_contents($url,false,$context);
        $doc = new domDocument();
        @$doc->loadHTML($str);
        $xml = simplexml_import_dom($doc);
        $n = 0;
        foreach($xml->xpath("//a") as $a) {
            $href = $a->attributes()->href."";
            if(!preg_match("/^http/",$href)) continue;
            $domain = mod_url::get($href)->_domain();
            if(in_array($domain,array($requestDomain,"solomono.ru"))) continue;
            $n++;
            seo_page::add($href);
        }
        if($n<20) return "done";
        return $page+1;
    }
    
}
