<? class reflex_mysql_admin_query extends mod_controller {

public static function indexTest() { return mod_superadmin::check(); }
public static function postTest() { return mod_superadmin::check(); }
public static function indexTitle() { return "Запросы MySQL"; }
public static function index($preview=null) {
    admin::header("Запрос MySQL");

    echo "<form style='padding:40px;' method='post' >";
    echo "<textarea style='width:100%;' name='q' >".htmlspecialchars($_POST["q"],ENT_QUOTES)."</textarea>";
    echo "<input type='submit' value='Отправить' >";
    echo "</form>";

    echo "<div style='padding:40px;' >";

    $start = microtime(true);
    if($_POST["q"]) self::sendQuery($_POST["q"]);
    $time = microtime(true) - $start;
    echo number_format($time,2)." с.";

    echo "</div>";

    admin::footer();
}
public static function indexFailed() { admin::fuckoff(); }

// -----------------------------------------------------------------------------

public static function sendQuery($q) {

    $ret = "";

    try {
        reflex_mysql::query($q);
        $arr = reflex_mysql::get_array();
    } catch (Exception $ex) {
        mod::msg($ex->getMessage(),1);
        return;
    }

    $ret.= sizeof($arr)." rows: <br/>";

    $ret.="<table style='width:100%;' >";
    foreach($arr as $n=>$row) {

        if($n==0) {
            $ret.="<tr>";
            foreach($row as $key=>$cell)
                $ret.="<td style='padding:5px;border:1px solid #ededed;font-weight:bold;' >$key</td>";
            $ret.="</tr>";
        }

        $ret.="<tr>";
        foreach($row as $cell)
            $ret.="<td style='padding:5px;border:1px solid #ededed;' >$cell</td>";
        $ret.="</tr>";
    }
    $ret.="</table>";
    echo $ret;
}

// -----------------------------------------------------------------------------

} ?>
