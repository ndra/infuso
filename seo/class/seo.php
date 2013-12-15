<?

/**
 * Контроллер отчета по продвижению
 **/
class seo extends mod_controller {

    public static function indexTest() {
        return mod_superadmin::check();
    }
    
    public static function indexFailed() {
        admin::fuckoff();
    }
    
    public static function indexTitle() {
        return "Отчет по продвижению";
    }

    public static function index_list() {
        admin::header("Отчет по продвижению");
        tmp::exec("seo:all");
        admin::footer();
    }

    public function index_domain($p) {
        ini_set('memory_limit', '512M');
        admin::header("Отчет по продвижению");
        tmp::exec("seo:domain",seo_domain::get($p["id"]));
        admin::footer();
    }
    
    public function index_test() {
        $x = mod::service("seoQueryInfo")->source("google")->info("верное рещение");
        var_export($x);
        
    }

    public static function normalizeDomain($domain) {
        //$domain = preg_replace("/^www\./","",$domain);
        //preg_match("/[a-z\d\.\_\-]*/",$domain,$matches);
        //return $matches[0];
        
        return $domain;
    }
    
    public static function postTest() {
        return true;
    }
    
    public function post_filterByDate($p) {
        unset($p['cmd']);
        $url = mod_action::get("seo", "domain", $p)->url();
        header("location: {$url}");    
    }

}
