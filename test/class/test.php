<?

namespace infuso\test;

class tester extends \infuso\core\controller {

    public function indexTest() {
        return true;
    }
    
    public function index($p) {

		\tmp::header();
		
        $root = \Infuso\ActiveRecord\Record::get("reflex_editor_root",10218);
        $items = $root->getList();
        //var_export($root->data());
        var_export($items->params());
       

		\tmp::footer();
        
    }

}
