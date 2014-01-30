<?

/**
 * Контроллер ручного режима импорта товаров
 **/ 
class eshop_1c_import extends mod_controller {

	public static function indexTitle() {
	    return "Ручная загрузка CommerceML";
	}

	public static function indexTest() {
	    return user::active()->checkAccess("eshop:1CExchange");
	}

	public static function postTest() {
	    return user::active()->checkAccess("eshop:1CExchange");
	}

	public static function indexFailed() {
	    return admin::fuckoff();
	}

	public static function index() {
	    admin::header("Импорт CommerceML");
	    echo "<div style='padding:40px;' >";
	    inx::add(array(
	        "type" => "inx.mod.eshop.import",
	    ));
	    echo "</div>";
	    admin::footer();
	}

	public static function post_importXML($p) {
	    $done = eshop_1c_exchange::importCatalog();
	    if($done)
	        eshop_1c_exchange::from(0);
	    return array(
	        "done" => $done,
	        "log" => "Импортировано товаров: ".eshop_1c_exchange::from(),
	    );
	}

	public static function post_offersXML($p) {
	    $done = eshop_1c_exchange::importOffers();
	    if($done)
	        eshop_1c_utils::importComplete();
	    return array(
	        "done" => $done,
	        "log" => "Импортировано предложений: ".eshop_1c_exchange::from(),
	    );
	}

	public static function post_start($p) {
	    eshop_1c_exchange::from(0);
	    eshop_1c_utils::importBegin();
	}

}
