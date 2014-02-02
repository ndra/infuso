<?

namespace infuso\test;

class tester extends \infuso\core\controller {

    public function indexTest() {
        return true;
    }
    
    public function index($p) {

		\tmp::header();
		
		$c = \infuso\core\post::getControllerClass("Infuso:Cms:Reflex:controller:views");
		var_export($c);
		
		\tmp::footer();
        
    }

}
