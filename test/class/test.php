<?

class test extends \infuso\core\controller {

    public function indexTest() {
        return true;
    }
    
    public function index() {

		tmp::header();
		
		$user = \infuso\ActiveRecord\Record::create("user");

		tmp::footer();
        
    }

}
