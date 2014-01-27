<?

/**
 * Контроллер просмотрщика лога
 **/
class mod_log_browser extends mod_controller {

	public static function indexTest() {
		return mod_superadmin::check();
	}

	public static function indexFailed() {
		return admin::fuckoff();
	}

	public static function indexTitle() {
		return "Просмотр лога";
	}

	public static function index() {

	    admin::header("Просмотр лога");
	    echo "<div style='padding:20px 40px 20px 40px;' >";

	    inx::add(array(
			"type"=>"inx.button",
			text=>"Очистить лог",
			icon=>"bin",
			onclick=>"if(!confirm('Очистить лог?')) return;this.call({cmd:'mod:log:browser:clear'},function(){ window.location.reload(); })"
		));
	    echo "<br/><br/>";

	    $date = date("Y-m-d");
	    $path = "/mod/trace/$date.txt";
	    $ret = file::get($path)->data();
        echo nl2br(htmlspecialchars($ret));
        // ,ENT_SUBSTITUTE

	    echo "</div>";
	    admin::footer();
	}

	public static function postTest() {
		return mod_superadmin::check();
	}

	public static function post_clear() {
	    file::get("/mod/trace/")->delete(1);
	}

}
