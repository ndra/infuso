<?

namespace infuso\test;

class tester extends \infuso\core\controller {

    public function indexTest() {
        return true;
    }
    
    public function index_fuck($p) {

		\tmp::header();
		
		$a = \mod::action("infuso\\test\\tester","fuck",array("id"=>123,"sss" => 5555));
		$url = $a->url();
		echo "<a href='{$url}' >{$url}</a>";

		\tmp::footer();
        
    }

}
