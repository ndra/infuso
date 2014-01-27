<?

tmp::jq();
tmp::js("/admin/res/admin.js");

tmp::exec("bar");
tmp::exec("dropdown");

/*css:
.gveialugfd-error {background:#cc0000;2px solid white;padding:20px;color:white;margin:0px 0px 10px 0px;border-radius: 10px;}
.gveialugfd-ok {background:#00aa00;2px solid white;padding:20px;color:white;margin:0px 0px 10px 0px;border-radius: 10px;}
*/

foreach(mod_log::messages() as $msg) {
    if(!$msg->error()) {
        echo "<div class='gveialugfd-ok' >";
        echo $msg->text();
        echo "</div>";
    } else {
        echo "<div class='gveialugfd-error' >";
        echo $msg->text();
        echo "</div>";
    }
}

?>
