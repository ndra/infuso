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
            tmp::header();
            tmp::footer();
            return;
        }
    
        tmp::exec("/board/report/workers");

    }

    public function index_worker($p) {

        $user = user::get($p["id"]);

        // Параметры задачи
        if(!user::active()->checkAccess("board/showUserReport",array(
            "task" => $task
        ))) {
            mod::msg(user::active()->errorText(),1);
            tmp::header();
            tmp::footer();
            return;
        }

        tmp::exec("/board/report/worker",array(
            "user" => $user,
        ));
    }
    
    public function index_projects() {
    
        $user = user::get($p["id"]);

        // Параметры задачи
        if(!user::active()->checkAccess("board/showProjectsReport",array(
            "task" => $task
        ))) {
            mod::msg(user::active()->errorText(),1);
            tmp::header();
            tmp::footer();
            return;
        }

        tmp::exec("/board/report/projects");
    
    }

}
