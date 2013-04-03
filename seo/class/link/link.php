<? class seo_link extends reflex {

public static function all() { return reflex::get(get_class()); }
public static function get($id) { return reflex::get(get_class(),$id); }
public function reflex_url() { return $this->data("href"); }
public function donor() { return reflex::get("seo_page",$this->data("donor")); }

public function reflex_beforeStore() {
    $domain = mod_url::get($this->data("href"))->_domain();
    $this->data("domain",seo::normalizeDomain($domain));
    $domain = mod_url::get($this->donor()->title())->_domain();
    $this->data("donorDomain",seo::normalizeDomain($domain));
}

} ?>
