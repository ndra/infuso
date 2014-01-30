<? class seo_query_positionGrabberYandexXML extends mod_controller{

public static function indexTest() { return mod_superadmin::check(); }
public static function index() {
    $n = self::get(seo_query::all()->one(),39);
    echo $n;
}

// -----------------------------------------------------------------------------

public static function get($query,$region) {

    $doc =
"<?xml version='1.0' encoding='UTF-8' ?".">
<request>
    <query>{$query->title()}</query>
    <maxpassages>0</maxpassages>
    <groupings>
        <groupby attr='d' mode='deep' groups-on-page='100' docs-in-group='1' curcateg='-1'/>
    </groupings>
</request>";

    $context = stream_context_create(array(
        'http' => array(
            'request_fulluri' => true,
            'method'=>"POST",
            'header'=>"Content-type: application/xml\r\n".
                      "Content-length: " . strlen($doc),
            'content'=>$doc
        )
    ));

    $response = file_get_contents("http://xmlsearch.yandex.ru/xmlsearch?user=studiondra&key=03.126111818:6b17562096461305b56eda591c9b233c&lr=$region", true, $context);

    $xml = simplexml_load_string($response);

    if(!$xml) {
        $query->_domain()->log("Error loading Yandex.XML");
        return array(
            "position" => 99999,
            "url" => false,
        );
    }

    if($e = $xml->response->error) {
        $query->_domain()->log("Yandex.XML: ".$e);
        return array(
            "position" => 99999,
            "url" => false,
        );
    }

    $n = 1;
    foreach($xml->xpath("//doc") as $doc) {
        if(seo::normalizeDomain($doc->domain)==seo::normalizeDomain($query->_domain()->title()))
        return array(
            "position" => $n,
            "url" => $doc->url."",
        );
        $n++;
    }

    return 99999;
}

} ?>
