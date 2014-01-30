<?

class seo_query_positionGrabberGoogle extends mod_controller{

    // Используется для тестирования
    public static function indexTest() {
        return mod_superadmin::check();
    }

    public static function index() {
        $query = seo_query::all()->one();
        $n = self::get($query);
        var_export($query->data());
        echo "<hr/>";
        echo $n;
    }

    // Метод определения позиций в гугле основан на том, что все url в результатах
    // поиска заключены в тэг <cite>
    public static function get($query) {

        $q = urlencode($query->title());
        $str = file_get_contents("https://www.google.ru/search?sourceid=chrome&ie=UTF-8&q={$q}&num=100");

        if($str) {

            $doc = new domDocument();
            @$doc->loadHTML($str);
            $xml = simplexml_import_dom($doc);

            $n = 1;
            foreach($xml->xpath("//cite") as $cite) {

                $domain = seo::normalizeDomain($cite."");
                if(seo::normalizeDomain($query->_domain()->title())==$domain)
                    return array(
                        "position" => $n,
                        "url" => $cite."",
                    );

                $n++;
            }
        }

        return array(
            "position" => 99999,
            "url" => false,
        );
    }

}
