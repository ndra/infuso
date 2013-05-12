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
        
        tmp::header();
        inx::add(array(
            "type" => "inx.mod.board.task",
            "showMore" => true,
            "taskID" => 1699,
        ));
        tmp::footer();
        
        
    }

}
