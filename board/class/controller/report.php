<?

class board_controller_report extends mod_controller {

    public function indexTest() {
        return user::active()->exists();
    }

    /**
     * Экшн получения списка задач
     **/             
    public static function index_workers($p) {

        // Параметры задачи
        if(!user::active()->checkAccess("board/showUserReport",array(
            "task" => $task
        ))) {
            mod::msg(user::active()->errorText(),1);
            return;
        }
    
        tmp::exec("/board/report/workers");

    }

}
