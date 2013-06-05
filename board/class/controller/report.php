<?

class board_controller_report extends mod_controller {

    public function indexTest() {
        return user::active()->exists();
    }

    public function postTest() {
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
    
        // Параметры задачи
        if(!user::active()->checkAccess("board/showProjectsReport")) {
            mod::msg(user::active()->errorText(),1);
            tmp::header();
            tmp::footer();
            return;
        }

        tmp::exec("/board/report/projects");
    
    }
    
    public function index_projectDetailed($p) {

        $project = board_project::get($p["projectID"]);

        // Параметры задачи
        if(!user::active()->checkAccess("board/showProjectsReport")) {
            mod::msg(user::active()->errorText(),1);
            tmp::header();
            tmp::footer();
            return;
        }

        tmp::exec("/board/report/project-detailed", array(
            "project" => $project,
            "params" => $p,
		));

    }

    /**
     * Контроллер для ленты с моей активностью за день
     **/
    public function post_getMyDayActivity() {

        $user = user::active();
        $ret = array(
            "tasks" => array(),
        );
        foreach(board_task_log::all()->eq("userID",$user->id())->gt("timeSpent",0)->desc("created")->limit(0)->eq("date(created)",util::now()->date()) as $log) {
            $time = $log->pdata("created");
            $duration = $log->data("timeSpent") * 3600;
            $start = $time->stamp() - $duration;
            $ret["tasks"][] = array(
                "start" => $start - util::now()->date()->stamp(),
                "duration" => $duration,
                "title" => $log->task()->title(),
                "taskID" => $log->task()->id(),
            );
        }
        return $ret;
    }

    /**
     * Контроллер для ленты с моей активностью за день
     **/
    public function index_vote() {

        tmp::exec("/board/report/vote");

    }

}
