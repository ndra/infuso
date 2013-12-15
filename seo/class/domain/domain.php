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
            if($position>=1 && $position<=10) {
                $ids[] = $query->id();
            }
        }
        return $this->queries()->eq("id",$ids);
    }

	/**
	 * Экстра данные
	 **/
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
    
    public function normalizeUrl($url) {
    
        $url = mod_url::get($url);
        
        if(!$url->domain()) {
        	$url->domain($this->data("domain"));
        }
        
        if(!$url->scheme()) {
        	$url->scheme("http");
        }
        
        return (string) $url;
    }

}
