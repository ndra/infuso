<? class moduleManager_newModule extends mod_controller{

/******************************************************************************/
// Настройки прямого доступа

public static function indexTest() { return mod_superadmin::check(); }
public static function postTest() { return mod_superadmin::check(); }
public static function indexTitle() { return "Создание нового модуля"; }
public static function index() {
    admin::header("Создание нового модуля");
    echo "<div style='text-align:center;'>";
    echo "<div style='margin:200px auto 0px auto;width:500px;' >";

    inx::add(array(
        "type"=>"inx.mod.moduleManager.newModule",
    ));

    echo "</div></div>";
    admin::footer();
}
public static function indexFailed() { admin::fuckoff(); }

/******************************************************************************/


public static function post_create($p) {

    $name = trim($p["name"]);
    
    $l = 'qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM';
    if(!preg_match("/^[$l][{$l}1234567890]*/", $name)) {
        mod::msg("Недопустимое имя модуля",1);
        return false;
    }
    
    if(file::get("/$name/")->exists()) {
        mod::msg("Модуль <b>$name</b> уже существует",1);
        return false;
    }
    
    file::mkdir($name);
    $info = "
[tmp]
path = /templates/

[mysql]
path = /tables/

[moduleManager]
edit[] = files
edit[] = templates
edit[] = inx
edit[] = tables
	";
    file::get("$name/info.ini")->put($info);
    mod::msg("Модуль <b>$name</b> создан");
    
}

} ?>
