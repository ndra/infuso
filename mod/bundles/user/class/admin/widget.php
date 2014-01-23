<?

class user_admin_diget extends admin_widget {

    public function test() {
        return mod_superadmin::check();
    }
    
    public function exec() {
        $url = mod::action("user_admin_report")->url();
        echo "<a href='$url' >Пользователи</a> ";
        
		$url = mod::action("user_admin_roles")->url();
        echo "<a href='$url' >Роли</a>";
    }

}
