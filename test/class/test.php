<?

namespace infuso\test;

class tester extends \infuso\core\controller {

    public function indexTest() {
        return true;
    }
    
    public function index_fuck($p) {

		\tmp::header();
		
		$user = \user::get(22);
		//$user->data("firstName",rand());
		var_export($user->data());

		\tmp::footer();
        
    }

}
