<?

class test extends \infuso\core\controller {

	public function indexTest() {
	    return true;
	}
	
	public function index() {
	    
	    $command = mod::service("db")->command("select * from infuso_eshop_item");
	    $result = $command->exec();
	    
	    foreach($result as $item) {
	        var_export($item);
			echo "<hr/>";
	    }
	    
	}

}
