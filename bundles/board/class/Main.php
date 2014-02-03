<?

namespace Infuso\Board;

use \User;
use \Tmp;

class Main extends \Infuso\Core\Controller {

    public static function indexTest() {
        return user::active()->exists();
    }
    
    public static function indexFailed() {
        tmp::exec("board:login");
    }
    
    public static function index() {
        tmp::exec("/board/main");
    }

    public function index_test() {
        
        tmp::header();
        
        $items = Task::visible();
        echo $items->count();

        tmp::footer();
        
        
    }

}
