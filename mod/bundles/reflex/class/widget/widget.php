<?

class reflex_widget extends admin_widget {

    public function exec() {

        if(user::active()->checkAccess("admin:showInterface")) {
            $url = mod_action::get("reflex_editor_controller")->url();
            echo "<h2><a href='{$url}' >Каталог</a></h2>";
        }

        if(mod_superadmin::check()) {
            $url = mod_action::get("reflex_mysql_admin_query")->url();
            echo "<a href='{$url}' >Запрос MySQL</a><br/>";
        }
        
        if(mod_superadmin::check()) {
            $url = mod_action::get("reflex_admin_tables")->url();
            echo "<a href='{$url}' >Таблицы</a><br/>";
        }

        if(mod_superadmin::check()) {
            $url = mod_action::get("reflex_sync")->url();
            echo "<a href='{$url}' >Синхронизация базы</a><br/>";
        }

    }

}
