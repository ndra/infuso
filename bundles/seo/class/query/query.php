<?

class seo_query extends reflex implements mod_handler {

    public static function all() {
        return reflex::get(get_class())->joinByField("domain")->neq("seo_domain.id",0)->asc("priority");
    }

    public static function get($id) {
        return reflex::get(get_class(),$id);
    }

    public function reflex_title() {
        return $this->data("query");
    }

    public function _domain() {
        return seo_domain::get($this->data("domain"));
    }

    public function reflex_parent() { return $this->_domain(); }

    public function reflex_cleanup() {
        if(!$this->_domain()->exists()) return true;
    }

    public function positions() {
        return seo_query_position::all()->eq("queryID",$this->id());
    }

    public function reflex_children() {
        return array(
            $this->positions()->title("Позиции"),
        );
    }

    public function reflex_repair() {
        $this->setUpdateTime();
    }

    public function setUpdateTime() {
        $d = 0;
        foreach($this->_domain()->engines() as $engine) {
            $date = $this->positions()->eq("engineID",$engine->id())->max("date");
            $date = util::date($date)->stamp();
            if($d==0) {
                $d = $date;
            } else {
                if($date<$d) $d = $date;
            }
        }
        $this->data("update",util::date($date));
    }

    public static function refreshLast() {

        $now = util::now()->notime()."";
        $queries = self::all()
            ->joinByField("domain")
            ->where("date(`update`)<>'$now' or `update` is null ");

        $query = $queries->one();

        if($query->exists()) {
            $query->refresh();
        }
    }

    public function on_mod_init() {
        reflex_task::add(array(
            "class" => get_class(),
            "method" => "refreshLast"
        ));
    }

    public function refresh() {

        foreach($this->_domain()->engines() as $engine) {
            $position = seo_query_position::all()->eq("date",util::now()->notime())->eq("engineID",$engine->id())->eq("queryID",$this->id())->one();
            if(!$position->exists()) {
                $position = reflex::create("seo_query_position",array(
                    "engineID" => $engine->id(),
                    "queryID" => $this->id(),
                ));
                $callback = $engine->data("callback");
                $callback = explode("::",$callback);
                $result = call_user_func($callback,$this,$engine->data("callbackParam"));
                $position->data("position",$result["position"]);
                $position->data("url",$result["url"]);
                return;
            }
        }
        $this->setUpdateTime();
    }

}
