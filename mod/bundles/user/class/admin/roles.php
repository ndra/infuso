<?

class user_admin_roles extends mod_controller {

    public function indexTest() {
        return mod_superadmin::check();
    }
    
    public function index() {
        tmp::exec("/user/admin/roles");
    }

}