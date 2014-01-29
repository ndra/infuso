<?

class test extends \infuso\core\controller {

    public function indexTest() {
        return true;
    }
    
    public function index() {
        
        $preview = file::get("1.jpg")->preview();
        echo $preview;
        
    }

}
