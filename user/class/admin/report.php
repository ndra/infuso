<?

class user_admin_report extends mod_controller {

    public function indexTest() {
        return user::active()->checkAccess("admin");
    }
    
    public function index() {
        tmp::exec("/user/admin/report");
    }

}