<?

class board extends mod_controller {

    public static function indexTest() {
        return user::active()->exists();
    }
    
    public static function indexFailed() {
        tmp::exec("board:login");
    }
    
    public static function index() {
        tmp::header();
        tmp::reset();
        inx::add("inx.mod.board.main");
        tmp::footer();
    }

    public function index_test() {
        echo board_task::get(1514)->subtasks()->count();
    }

}
