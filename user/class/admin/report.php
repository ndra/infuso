<?

class user_admin_report extends mod_controller {

    public function indexTest() {
        return true;
    }
    
    public function index() {
        tmp::exec("/user/admin/report");
    }

}