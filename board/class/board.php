<?

class board extends mod_controller {

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
        inx::add(array(
            "type" => "inx.mod.board.task",
            "taskID" => 1552,
        ));
        tmp::footer();
        
        
    }

}
