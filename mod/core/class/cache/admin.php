<?

/**
 * Драйвер кэша для xcache
 **/
class mod_cache_admin extends mod_controller {

	public static function indexTitle() {
		return "Кэш";
	}

	public static function indexTest() {
		return mod_superadmin::check();
	}

	public static function postTest() {
		return mod_superadmin::check();
	}

	public static function index() {
	    admin::header("Кэш");
	    $files = file::get("/mod/cache/")->search();
	    echo "<div style='padding:40px;' >";
	    echo "Cached {$files->length()} items (".round($files->size()/1000000)." Mb)";
	    echo "<br/><br/>";
	    inx::add(array("type"=>"inx.button","text"=>"Очистить кэш","onclick"=>"this.call({cmd:'mod/cache/admin/clear'},function() {window.location.reload()})"));
	    echo "</div>";
	    admin::footer();
	}

	public static function post_clear() {
	    mod::service("cache")->clear();
	}

}
