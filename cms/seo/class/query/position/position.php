<?

class seo_query_position extends reflex {

	public static function all() { return reflex::get(get_class())->desc("date"); }
	
	public static function today() { return self::all()->eq("date",util::now()); }

	public static function get($id) { return reflex::get(get_class(),$id); }
	
	public static function reflex_root() {
		return array(
			self::all()->title("Позиции")->param("tab","system")
		);
	}

	public function reflex_parent() { return $this->query(); }

	public function query() { return seo_query::get($this->data("queryID")); }
	
	public function _domain() { return seo_query::get($this->data("queryID"))->_domain(); }
	
	public function engine() { return seo_engine::get($this->data("engineID")); }

	public function reflex_afterStore() { $this->query()->setUpdateTime(); }
	
	public function reflex_afterDelete() { $this->query()->setUpdateTime(); }

	public function reflex_beforeCreate() {
	    $this->data("date",util::now());
	    $this->data("position",99999);
	}

	public static function best() {
	    $domains = seo_domain::all()->eq("public",1)->distinct("id");
	    $qq = seo_query::all()->eq("domain",$domains)->distinct("id");
	    return self::today()->eq("queryID",$qq)->leq("position",3)->geq("position",1)->asc("position");
	}
	
}
