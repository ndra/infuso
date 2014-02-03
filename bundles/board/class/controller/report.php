<?

namespace Infuso\Board\Controller;

use \user, \util;

class Report extends \Infuso\Core\Controller {

    public function indexTest() {
        return user::active()->exists();
    }

    public function postTest() {
        return \user::active()->exists();
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

        $tasks = \board_task_time::all()
            ->eq("userID",$user->id())
            ->limit(0)
            ->eq("date(begin)",util::now()->date());

        foreach($tasks as $log) {
            $start = $log->pdata("begin")->stamp();
            $end = $log->data("end") ? $log->pdata("end")->stamp() : util::now()->stamp();
            $duration = $end - $start;
            $ret["tasks"][] = array(
                "start" => $start - util::now()->date()->stamp(),
                "duration" => $duration,
                "title" => $log->task()->title(),
                "taskID" => $log->task()->id(),
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
