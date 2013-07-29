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
        if(!user::active()->checkAccess("board/showReportUsers",array(
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
    
    public function index_projects($p) {
    
        // Параметры задачи
        if(!user::active()->checkAccess("board/showProjectsReport")) {
            mod::msg(user::active()->errorText(),1);
            tmp::header();
            tmp::footer();
            return;
        }

        tmp::exec("/board/report/projects", array(
            "params" => $p,
        ));
    
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

    public function index_done($p) {

        // Параметры задачи
        if(!user::active()->checkAccess("board/showReportDone")) {
            mod::msg(user::active()->errorText(),1);
            tmp::header();
            tmp::footer();
            return;
        }

        tmp::exec("/board/report/done", array(
            "params" => $p,
        ));

    }

    /**
     * Контроллер для ленты с моей активностью за день
     **/
    public function post_getMyDayActivity($p) {

        // Параметры задачи
        if(!user::active()->checkAccess("board/showAllUsersDailyActivity")) {
            return false;
        }

        $user = $p["userID"] ? user::get($p["userID"]) : user::active();
        $ret = array(
            "tasks" => array(),
            "user" => array(
                "userpick20" => (string)$user->userpick()->preview(20,20),
            )
        );

        $tasks = board_task_log::all()
            ->eq("userID",$user->id())
            ->gt("timeSpent",0)
            ->desc("created")
            ->limit(0)
            ->eq("date(created)",util::now()->date());

        foreach($tasks as $log) {
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

        // Выполняющиеся задания
        $tasks = board_task::all()
            ->eq("responsibleUser",$user->id())
            ->eq("status",board_task_status::STATUS_IN_PROGRESS)
            ->isNull("paused")
            ->limit(0);

        foreach($tasks as $task) {
            $time = util::now();
            $duration = $task->timeSpentProgress();
            $start = $time->stamp() - $duration;
            $ret["tasks"][] = array(
                "start" => $start - util::now()->date()->stamp(),
                "duration" => $duration,
                "title" => $task->title(),
                "taskID" => $task->id(),
                "inprogress" => true,
            );
        }

        return $ret;
    }
    
    public function index_gallery($p) {

        // Параметры задачи
        if(!user::active()->checkAccess("board/showReportVote")) {
            mod::msg(user::active()->errorText(),1);
            tmp::header();
            tmp::footer();
            return;
        }

        tmp::exec("/board/report/gallery");
    }

    /**
     * Контроллер для ленты с моей активностью за день
     **/
    public function post_getUsers() {

        // Параметры задачи
        if(!user::active()->checkAccess("board/showAllUsersDailyActivity")) {
            return array();
        }

        $ret = array();
        $users = user::all()->like("roles","boardUser")->neq("id",user::active()->id());
        foreach($users as $user) {
            $ret[] = array(
                "userID" => $user->id(),
            );
        }
        return $ret;
    }

    /**
     * Контроллер для ленты с моей активностью за день
     **/
    public function index_vote() {

        // Параметры задачи
        if(!user::active()->checkAccess("board/showReportVote")) {
            mod::msg(user::active()->errorText(),1);
            tmp::header();
            tmp::footer();
            return;
        }

        tmp::exec("/board/report/vote");

    }

    /**
     * Контроллер для ленты с моей активностью за день
     **/
    public function index_projectChart($p) {

        $project = board_project::get($p["id"]);

        // Параметры задачи
        if(!user::active()->checkAccess("board/showReportProjectActivity",array(
            "project" => $project,
        ))) {
            mod::msg(user::active()->errorText(),1);
            tmp::header();
            tmp::footer();
            return;
        }

        tmp::exec("/board/report/chart",array(
            "params" => $p,
        ));

    }


}
