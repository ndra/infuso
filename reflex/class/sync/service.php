<?

class reflex_sync_service extends mod_service {

    public static function indexTest() {
        return true;
    }

    public function getData($p) {

        $service = mod_service::get("reflexSync");
        $service->checkToiken();

        $data = array();
        $class = $p["class"];
        $items = reflex::get($class)
            ->asc("id")
            ->gt("id",$p["id"]);

        foreach($items as $item) {
            $data[] = $item->data();
            reflex::freeAll();
        }

        header("content-type:application/json");
        echo json_encode($data);

    }

}
