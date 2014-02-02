<?

namespace infuso\test;

class tester extends \infuso\core\controller {

    public function indexTest() {
        return true;
    }
    
    public function index($p) {

		\tmp::header();
		
        //$items = \Infuso\Board\Task::all();
        //echo $items->count();
        
        $x = "infuso\\board\\collectionbehaviour";
        //new $x;
        $z = \mod::service("classmap")->testClass($x);
        //var_export($z);
       

		\tmp::footer();
        
    }

}
