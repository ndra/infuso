<?

class reflex_admin_tables extends mod_controller {

    public function indexTest() {
        return mod_superadmin::check();
    }
    
    public function index($p) {
        tmp::exec("/reflex/admin/tables",array(
            "class" => $p["class"],
            "search" => $p["search"],
        ));
    }

}