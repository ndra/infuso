<? class seo_link_page extends reflex {

public static function all() { return reflex::get(get_class()); }
public static function get($url) { return self::all()->eq("url",$url)->one(); }

public function reflex_children() {
    return array(
        seo_link::all()->title("Ссылки")
    );
}

public function reflex_title() { return $this->data("url"); }
public function reflex_url() { return $this->data("url"); }

// -----------------------------------------------------------------------------

public static function add($url) {
    $item = self::get($url);
    if(!$item->exists())
        $item = reflex::create(get_class(),array("url"=>$url));
    return $item;
}

// -----------------------------------------------------------------------------

public static function loadSome() {
    for($i=0;$i<10;$i++)
        self::all()->asc("updated")->one()->loadHTML();
}

public function loadHTML() {
    if(!$this->exists()) return;
    $ctx = stream_context_create(array(
        'http' => array(
            'timeout' => 5
            )
        )
    );
    $str = @file_get_contents($this->data("url"),0,$ctx);

    if(!$str) $this->delete();
    $str = util::str($str)->decode()."";
    $this->data("html",$str);
    $this->createLinks();
    $this->data("updated",util::now());
    reflex::storeAll();
}

// -----------------------------------------------------------------------------

public function xml() {
    $doc = new domDocument();
    @$doc->loadHTML("<META http-equiv='Content-Type' content='text/html; charset=utf-8'> ".$this->data("html"));
    $xml = simplexml_import_dom($doc);
    return $xml;
}

public function links() {
    return seo_link::all()->eq("donor",$this->id());
}

public function createLinks() {
    $this->links()->delete();
    foreach($this->xml()->xpath("//a") as $a) {
        $href = $a->attributes()->href."";
        if(!preg_match("/^http:\/\//",$href)) continue;
        reflex::create("seo_link",array(
            "donor" => $this->id(),
            "href" => $href,
            "title" => $a."",
        ));
    }
}

// -----------------------------------------------------------------------------

} ?>
