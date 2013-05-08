<?

class board_controller_report extends mod_controller {

    public function indexTest() {
        return user::active()->exists();
    }

    /**
     * Экшн получения списка задач
     **/             
    public static function index_workers($p) {
    
        tmp::exec("/board/report/workers");

    }

}
