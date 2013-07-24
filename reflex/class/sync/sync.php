<?

class reflex_sync extends mod_controller {

    public static function indexTest() {
        return true;
    }

    public static function postTest() {
        return mod_superadmin::check();
    }

    public function index() {
        tmp::exec("/reflex/admin/sync");
    }

    public function index_get($p) {

        $token = trim($this->param("token"));
        if(!$token) {
            throw new Exception("Token not set");
        }

        if($token!=$p["token"]) {
            throw new Exception("Bad token");
        }

        $data = array(
            "rows" => array(),
        );
        $class = $p["class"];

        $limit = $p["limit"] * 1;
        if(!$limit) {
            $limit = 100;
        }

        $items = reflex::get($class)
            ->asc("id")
            ->gt("id",$p["id"])
            ->limit($limit);

        $data["total"] = $items->count();

        $n = 0;
        foreach($items as $item) {
            $data["rows"][] = $item->data();
            $data["nextID"] = $item->id();
            reflex::freeAll();
            $n++;
        }

        if($n==0) {
            $data["completed"] = true;
        }

        header("content-type:application/json");
        $data = json_encode($data);
        $data = gzcompress($data);
        echo $data;

    }

    /**
     * Контроллер, возвращающий список классов
     **/
    public function post_getClassList() {

        $skip = $this->param("skip");
        if(!is_array($skip)) {
            $skip = array();
        }

        foreach(reflex::classes() as $class) {
            if(!in_array($class,$skip)) {
                $ret[] = $class;
            }
        }

        return $ret;

    }

    public function post_syncStep($p) {

        $class = $p["className"];

        $token = $this->param("remoteToken");
        $host = $this->param("remoteHost");
        $limit = $this->param("remoteLimit");

        if(!$limit) {
            $limit = 500;
        }

        $url = "http://$host/reflex_sync/get/class/".$class."/id/{$p[fromID]}/token/{$token}/limit/{$limit}";

        $data = file::http($url)->data();

        if(!$data) {
            mod::msg("No data received",1);
            return false;
        }

        $data = gzuncompress($data);

        $data = json_decode($data,1);
        if($data===null) {
            mod::msg("Json decode failed",1);
            return;
        }

        if($data["completed"]) {
            return array(
                "action" => "nextClass",
            );
        }

        $v = reflex::virtual($class);
        $table = $v->table()->prefixedName();

        if($p["fromID"]==0) {
            reflex_mysql::query("truncate table `$table` ");
            mod::msg("truncate $class");
        }

        foreach($data["rows"] as $row) {

            foreach($row as $key=>$val) {
                unset($row[$key]);
                $row["`".$key."`"] = '"'.mysql_real_escape_string($val).'"';
            }

            // Вставляем в таблицу
            $itemData = array();
            $insert = " (".implode(",",array_keys($row)).") values (".implode(",",$row).") ";
            $query = "insert into `$table` $insert ";
            reflex_mysql::query($query);
        }

        return array(
            "action" => "nextID",
            "nextID" => $data["nextID"],
            "log" => array(
                "class" => $class,
                "message" => $class.": {$data[nextID]} total {$data[total]}"
            ),
        );

    }

}
