<? 
    
class admin_seo extends mod_controller {

    public static function indexTest() {
        return mod_superadmin::check();
    }
    
    public static function indexTitle() {
        return "SEO-настройки";
    }
    
    public static function indexFailed() {
        admin::fuckoff();
    }
    
    public static function index() {
        admin::header("SEO-настройки");
            echo "<form style='padding:40px;' method='post' enctype='multipart/form-data'>";
            tmp::head("<style>.conftable{} .conftable td{vertical-align:middle;}</style>"); 
                tmp::exec("admin:seo");
            echo "<br/><br/>";
            echo "<input type='submit' value='Сохранить' />";
            echo "<input type='hidden' name='cmd' value='admin:seo:save' />";
            echo "</form>";
        admin::footer();
    }
    
    public static function postTest() {
        return mod_superadmin::check();
    }
    
    public static function post_save($p) {
        file::get("/robots.txt")->put($p["robots"]);
        if($_FILES["favicon"]["size"] > 0 && $_FILES["favicon"]["type"] == "image/x-icon")  
            file::get("/favicon.ico")->put(@file_get_contents($_FILES["favicon"]["tmp_name"]));
    }

}
