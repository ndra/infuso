<?

class reflex_mysql_admin_query extends mod_controller {

    public static function indexTest() {
        return mod_superadmin::check();
    }

    public static function postTest() {
        return mod_superadmin::check();
    }

    public static function indexTitle() {
        return "Запросы MySQL";
    }

    public static function index() {

        $params = array();

        if($_POST["q"]) {
            $start = microtime(true);
            $params["result"] = self::sendQuery($_POST["q"]);
            $time = microtime(true) - $start;
            $params["time"] = number_format($time,2)." с.";
        }


        tmp::exec("/reflex/admin/mysql-query",$params);
    }

    public static function indexFailed() {
        admin::fuckoff();
    }

    public static function sendQuery($q) {

        $ret = "";

        try {
            reflex_mysql::query($q);
            $arr = reflex_mysql::get_array();
            mod::service("log")->log(array(
                "type" => "reflex/mysql-admin-query",
                "text" => $q,
            ));
        } catch (Exception $ex) {
            mod::msg($ex->getMessage(),1);
            return;
        }

        return $arr;

    }

}