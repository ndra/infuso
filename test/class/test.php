<?

class test extends \infuso\core\controller {

    public function indexTest() {
        return true;
    }
    
    public function index() {

		tmp::header();
		
		echo user::all()->count();

		tmp::footer();
        
    }

}
