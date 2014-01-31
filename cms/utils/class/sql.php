<?

namespace infuso\cms\utils;

/**
 * Контроллер для выполнения запросов sql
 **/
class sql extends \infuso\core\controller {

	public function indexTest() {
	    return \infuso\core\superadmin::check();
	}
	
	public function index() {
	    \tmp::exec("/admin/utils/sql");
	}
	
}
