<?

class admin_menu {

    public static function get() {
    
        $menu = array();
        foreach(mod::all() as $mod) {
            if(!$mod_menu = mod::info($mod,"admin","menu")) continue;
            $mm = array();
            foreach($mod_menu as $class)
                if(self::test($class))
                    $mm[] = array(
                        "text"=>self::getTitle($class),
                        "icon"=>self::getIcon($class),
                        "url"=>"/$class/",
                        );
            if(sizeof($mm))
                $menu[$mod] = $mm;

        }
        return $menu;
    }
    
    private function getTitle($class) {
        if(!method_exists($class,"indexTitle")) return $class;
        return call_user_func(array($class,"indexTitle"));
    }
    
    private function getIcon($class) {
        if(!method_exists($class,"indexIcon")) return $false;
        return call_user_func(array($class,"indexIcon"));
    }
    
    // Коряво!!!!!!!!!
    // Надо чтобы тут выполнялся полный комплекс тестов для индекса, как в cmd::
    public static function test($class) {
        if(mod_superadmin::check()) return true;
        $r = @call_user_func(array($class,"indexTest"));
        return $r;
    }
    
}
